<?php
include __DIR__ . '/../../config/db.php';
$savedOrderId = filter_input(INPUT_GET, 'saved', FILTER_VALIDATE_INT);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Offline Order</title>
<style>
body { font-family: Arial, sans-serif; background: #f5f6fa; margin: 0; padding: 30px; }
.order-box { max-width: 760px; margin: auto; background: white; padding: 25px; border-radius: 12px; box-shadow: 0 3px 10px rgba(0,0,0,.08); }
h1 { color: #2c3e50; }
input, button { padding: 11px; margin: 6px 0; border: 1px solid #ddd; border-radius: 6px; }
input[type="text"], input[type="tel"], input[type="number"] { width: calc(100% - 26px); }
.cart-row { display: grid; grid-template-columns: 2fr 1fr 1fr auto; gap: 8px; align-items: center; }
.cart-row input { width: auto; }
button { background: #ff6b9d; color: white; border: 0; cursor: pointer; font-weight: bold; }
.remove { background: #777; }
.total { text-align: right; font-size: 1.2rem; font-weight: bold; margin: 15px 0; }
.saved-qr { margin: 20px 0; padding: 18px; background: #fff3cd; text-align: center; }
.saved-qr img { display: block; width: 220px; height: 220px; margin: 12px auto; }
a { color: #ff4f89; }
@media (max-width: 600px) { .cart-row { grid-template-columns: 1fr 1fr; } }
</style>
</head>
<body>
<main class="order-box">
<h1>Offline Order</h1>
<p><a href="dashboard.php">Back to dashboard</a></p>
<?php if ($savedOrderId): ?>
<section class="saved-qr">
    <h2>Order #<?php echo (int) $savedOrderId; ?> saved</h2>
    <p>Scan this QR code to view the customer receipt.</p>
    <img id="saved-order-qr" alt="QR code for offline order receipt">
    <p id="saved-order-link"></p>
    <button type="button" onclick="window.print()">Print order QR</button>
</section>
<?php endif; ?>
<section class="qr-box">
    <h2>Table QR code</h2>
    <p>Generate a QR code for a customer to open the menu and order from their table.</p>
    <input type="number" id="table-number" min="1" placeholder="Table number">
    <button type="button" id="generate-qr">Generate QR</button>
    <div id="qr-result" hidden>
        <img id="table-qr" alt="Table order QR code">
        <p id="qr-link"></p>
        <button type="button" onclick="window.print()">Print QR</button>
    </div>
</section>
<form action="../core/save_offline_order.php" method="post" id="offline-order-form">
<input type="text" name="customer_name" placeholder="Customer name" required>
<input type="tel" name="phone" placeholder="Phone number" required>
<div id="offline-cart"></div>
<button type="button" id="add-item">Add item</button>
<div class="total">Total: ₹<span id="offline-total">0.00</span></div>
<input type="hidden" name="items" id="offline-items" value="[]">
<input type="hidden" name="total" id="offline-total-input" value="0">
<button type="submit">Save Offline Order</button>
</form>
</main>
<script>
document.getElementById('generate-qr').addEventListener('click', () => {
    const table = document.getElementById('table-number').value.trim();
    if (!table || Number(table) < 1) return alert('Enter a valid table number.');
        const url = new URL(`../../public/index.html?table=${encodeURIComponent(table)}`, window.location.href).href;
    document.getElementById('table-qr').src = 'https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=' + encodeURIComponent(url);
    document.getElementById('qr-link').textContent = url;
    document.getElementById('qr-result').hidden = false;
});

const cart = [];
const savedOrderId = <?php echo $savedOrderId ? (int) $savedOrderId : 'null'; ?>;
if (savedOrderId) {
        const receiptUrl = new URL(`../../templeates/offline_receipt.php?id=${savedOrderId}`, window.location.href).href;
    document.getElementById('saved-order-qr').src = 'https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=' + encodeURIComponent(receiptUrl);
    document.getElementById('saved-order-link').textContent = receiptUrl;
}

const cartElement = document.getElementById('offline-cart');
function renderCart() {
    cartElement.innerHTML = cart.map((item, index) => `
        <div class="cart-row">
            <input type="text" placeholder="Item name" data-field="name" data-index="${index}" required>
            <input type="number" placeholder="Qty" min="1" value="1" data-field="quantity" data-index="${index}" required>
            <input type="number" placeholder="Price" min="0" step="0.01" value="0" data-field="price" data-index="${index}" required>
            <button type="button" class="remove" data-remove="${index}">Remove</button>
        </div>`).join('');
    cartElement.querySelectorAll('[data-field]').forEach(input => input.addEventListener('input', updateCart));
    cartElement.querySelectorAll('[data-remove]').forEach(button => button.addEventListener('click', () => {
        cart.splice(Number(button.dataset.remove), 1);
        renderCart();
        updateCart();
    }));
}
document.getElementById('add-item').addEventListener('click', () => {
    cart.push({ name: '', quantity: 1, price: 0 });
    renderCart();
});
document.getElementById('offline-order-form').addEventListener('submit', event => {
    updateCart();
    if (!cart.length || cart.some(item => !item.name.trim() || item.price <= 0)) {
        event.preventDefault();
        alert('Please complete every cart item with a name and price.');
    }
});
function updateCart() {
    cartElement.querySelectorAll('[data-field]').forEach(input => {
        const item = cart[Number(input.dataset.index)];
        item[input.dataset.field] = input.dataset.field === 'name' ? input.value : Number(input.value);
    });
    const total = cart.reduce((sum, item) => sum + item.price * item.quantity, 0);
    document.getElementById('offline-total').textContent = total.toFixed(2);
    document.getElementById('offline-total-input').value = total.toFixed(2);
    document.getElementById('offline-items').value = JSON.stringify(cart);
}
</script>
</body>
</html>
