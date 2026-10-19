<?php
include __DIR__ . '/../../config/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
	$orderId = filter_input(INPUT_POST, 'order_id', FILTER_VALIDATE_INT);
	$status = trim($_POST['order_status'] ?? '');
	$allowedStatuses = ['Order received', 'Preparing', 'Out for delivery', 'Delivered', 'Cancelled'];

	if ($orderId && in_array($status, $allowedStatuses, true)) {
		$statement = mysqli_prepare($conn, 'UPDATE online_orders SET order_status = ? WHERE id = ?');
		mysqli_stmt_bind_param($statement, 'si', $status, $orderId);
		mysqli_stmt_execute($statement);
		mysqli_stmt_close($statement);
	}

	header('Location: online_order.php');
	exit;
}

$orders = mysqli_query($conn, 'SELECT * FROM online_orders ORDER BY id DESC');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Online Orders</title>
<link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css">
<style>
body { font-family: Arial, sans-serif; background: #f5f6fa; margin: 0; padding: 30px; }
.orders-box { max-width: 1100px; margin: auto; background: white; padding: 25px; border-radius: 12px; overflow-x: auto; }
h1 { color: #2c3e50; }
a { color: #ff4f89; }
table { width: 100%; border-collapse: collapse; margin-top: 20px; }
th { background: #ff6b9d; color: white; text-align: left; }
th, td { padding: 12px; border-bottom: 1px solid #eee; vertical-align: top; }
ul { margin: 0; padding-left: 18px; }
.customer-map { margin-top: 8px; }
.customer-map summary { cursor: pointer; color: #ff4f89; }
.customer-map-canvas { width: 280px; height: 200px; margin-top: 8px; }
.customer-map-link { display: inline-block; margin-top: 8px; }
</style>
</head>
<body>
<main class="orders-box">
<h1>Online Orders</h1>
<p><a href="dashboard.php">Back to dashboard</a></p>
<table>
<thead><tr><th>ID</th><th>Customer</th><th>Phone</th><th>Table</th><th>Address</th><th>Customer Location</th><th>Track Map</th><th>Delivery Partner</th><th>Items</th><th>Total</th><th>Status</th><th>Date</th></tr></thead>
<tbody>
<?php if ($orders && mysqli_num_rows($orders) > 0): ?>
<?php while ($order = mysqli_fetch_assoc($orders)): ?>
<tr>
<td><?php echo (int) $order['id']; ?></td>
<td><?php echo htmlspecialchars($order['customer_name']); ?></td>
<td><?php echo htmlspecialchars($order['phone']); ?></td>
<td><?php echo htmlspecialchars($order['table_number'] ?? '-'); ?></td>
<td>
<?php
$deliveryAddress = trim($order['address'] ?? '');
if ($deliveryAddress !== ''):
	$addressMapUrl = 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($deliveryAddress);
?>
<a href="<?php echo htmlspecialchars($addressMapUrl, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener noreferrer"><?php echo htmlspecialchars($deliveryAddress); ?></a>
<?php else: ?>
Address not provided
<?php endif; ?>
</td>
<td>
<?php if ($order['customer_latitude'] !== null && $order['customer_longitude'] !== null): ?>
<?php
$customerLatitude = (float) $order['customer_latitude'];
$customerLongitude = (float) $order['customer_longitude'];
$customerMapUrl = 'https://www.google.com/maps/search/?api=1&query=' . rawurlencode($customerLatitude . ',' . $customerLongitude);
?>
<details class="customer-map" data-latitude="<?php echo htmlspecialchars((string) $customerLatitude, ENT_QUOTES, 'UTF-8'); ?>" data-longitude="<?php echo htmlspecialchars((string) $customerLongitude, ENT_QUOTES, 'UTF-8'); ?>">
<summary>Show customer location</summary>
<div class="customer-map-canvas" aria-label="Map showing the customer's location"></div>
<a class="customer-map-link" href="<?php echo htmlspecialchars($customerMapUrl, ENT_QUOTES, 'UTF-8'); ?>" target="_blank" rel="noopener noreferrer">Open in Google Maps</a>
</details>
<?php else: ?>
Location not available
<?php endif; ?>
</td>
<td><a href="../../public/index.html?track_order=<?php echo (int) $order['id']; ?>#track-order">Track order #<?php echo (int) $order['id']; ?></a></td>
<td><?php echo htmlspecialchars($order['delivery_partner']); ?></td>
<td><ul><?php foreach (json_decode($order['items'], true) ?: [] as $item): ?>
<li>
<?php echo htmlspecialchars($item['name'] ?? 'Item'); ?> x <?php echo (int) ($item['quantity'] ?? 0); ?>
<?php if (!empty($item['customization'])): ?>
<br><small>Customize: <?php echo htmlspecialchars($item['customization']); ?></small>
<?php endif; ?>
</li>
<?php endforeach; ?></ul></td>
<td>₹<?php echo number_format((float) $order['total'], 2); ?></td>
<td>
<form method="post">
<input type="hidden" name="order_id" value="<?php echo (int) $order['id']; ?>">
<select name="order_status" onchange="this.form.submit()" aria-label="Update status for order #<?php echo (int) $order['id']; ?>">
<?php foreach (['Order received', 'Preparing', 'Out for delivery', 'Delivered', 'Cancelled'] as $status): ?>
<option value="<?php echo htmlspecialchars($status); ?>" <?php echo ($order['order_status'] ?? 'Order received') === $status ? 'selected' : ''; ?>><?php echo htmlspecialchars($status); ?></option>
<?php endforeach; ?>
</select>
</form>
<button type="button" class="share-location" data-order-id="<?php echo (int) $order['id']; ?>">Share live location</button>
<small class="location-status" data-status-for="<?php echo (int) $order['id']; ?>"></small>
</td>
<td><?php echo htmlspecialchars($order['created_at'] ?? ''); ?></td>
</tr>
<?php endwhile; ?>
<?php else: ?><tr><td colspan="12">No online orders found.</td></tr><?php endif; ?>
</tbody>
</table>
</main>
<script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
<script>
document.querySelectorAll('.customer-map').forEach(locationDetails => {
	locationDetails.addEventListener('toggle', () => {
		if (!locationDetails.open || locationDetails.dataset.mapInitialized === 'true') return;
		if (!window.L) {
			locationDetails.querySelector('.customer-map-canvas').textContent = 'Map could not be loaded.';
			return;
		}

		const point = [
			Number(locationDetails.dataset.latitude),
			Number(locationDetails.dataset.longitude)
		];
		const map = L.map(locationDetails.querySelector('.customer-map-canvas')).setView(point, 16);
		L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
			attribution: '&copy; OpenStreetMap contributors'
		}).addTo(map);
		L.marker(point).addTo(map).bindPopup('Customer location').openPopup();
		locationDetails.dataset.mapInitialized = 'true';
		requestAnimationFrame(() => map.invalidateSize());
	});
});

const locationWatchers = new Map();

document.querySelectorAll('.share-location').forEach(button => {
	button.addEventListener('click', () => {
		const orderId = button.dataset.orderId;
		const status = document.querySelector(`[data-status-for="${orderId}"]`);
		if (!navigator.geolocation) {
			status.textContent = 'Location is not supported.';
			return;
		}
		if (locationWatchers.has(orderId)) {
			navigator.geolocation.clearWatch(locationWatchers.get(orderId));
			locationWatchers.delete(orderId);
			button.textContent = 'Share live location';
			button.disabled = false;
			status.textContent = 'Location sharing stopped.';
			return;
		}

		button.disabled = true;
		status.textContent = 'Waiting for location permission...';
		const watcherId = navigator.geolocation.watchPosition(async position => {
			const body = new URLSearchParams({
				order_id: orderId,
				latitude: position.coords.latitude,
				longitude: position.coords.longitude
			});
			try {
				const response = await fetch('update_order_location.php', { method: 'POST', body });
				if (!response.ok) throw new Error();
				status.textContent = `Sharing live location (${new Date().toLocaleTimeString()})`;
				button.textContent = 'Stop sharing';
				button.disabled = false;
				locationWatchers.set(orderId, watcherId);
			} catch {
				status.textContent = 'Could not update location.';
			}
		}, () => {
			button.disabled = false;
			status.textContent = 'Location permission was denied.';
		}, { enableHighAccuracy: true, maximumAge: 5000, timeout: 10000 });
		locationWatchers.set(orderId, watcherId);
	});
});
</script>
</body>
</html>
