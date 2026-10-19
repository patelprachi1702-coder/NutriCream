<?php
include __DIR__ . '/../../config/db.php';
$qrHost = $_SERVER['HTTP_HOST'] ?? '';
$qrHostName = strtolower(preg_replace('/:\d+$/', '', $qrHost));
if ($qrHostName === 'localhost' || $qrHostName === '127.0.0.1') {
    $serverAddress = gethostbyname(gethostname());
    if (filter_var($serverAddress, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4) && strpos($serverAddress, '127.') !== 0) {
        $port = parse_url('http://' . $qrHost, PHP_URL_PORT);
        $qrHost = $serverAddress . ($port ? ':' . $port : '');
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Offline Order QR</title>
<style>
body { font-family: Arial, sans-serif; background: #f5f6fa; margin: 0; padding: 30px; }
.order-box { max-width: 760px; margin: auto; background: white; padding: 25px; border-radius: 12px; box-shadow: 0 3px 10px rgba(0,0,0,.08); }
h1 { color: #2c3e50; }
button { background: #ff6b9d; color: white; border: 0; cursor: pointer; font-weight: bold; }
.table-qr { display: block; width: 220px; height: 220px; margin: 12px auto; }
.qr-box { margin: 20px 0; padding: 18px; background: #fff3cd; text-align: center; }
a { color: #ff4f89; }
</style>
</head>
<body>
<main class="order-box">
<h1>Offline Order QR</h1>
<p><a href="dashboard.php">Back to dashboard</a></p>
<section class="qr-box">
    <h2>Table Order QR Code</h2>
    <p>Generate and print this QR code. Customers scan it, choose their items, enter their table number, and send the order to the shop. No customer name or phone number is needed.</p>
    <button type="button" id="generate-qr">Generate Table Order QR</button>
    <div id="qr-result" hidden>
        <img id="table-qr" class="table-qr" alt="Reusable QR code for table orders">
        <p id="qr-link"></p>
        <button type="button" onclick="window.print()">Print Table QR</button>
    </div>
</section>
</main>
<script>
const qrHost = <?php echo json_encode($qrHost, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
document.getElementById('generate-qr').addEventListener('click', () => {
    const url = new URL('../../public/table-order.html', window.location.href);
    url.host = qrHost;
    document.getElementById('table-qr').src = 'https://api.qrserver.com/v1/create-qr-code/?size=220x220&data=' + encodeURIComponent(url);
    document.getElementById('qr-link').textContent = url.href;
    document.getElementById('qr-result').hidden = false;
});
</script>
</body>
</html>
