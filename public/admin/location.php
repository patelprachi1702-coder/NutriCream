<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Share Shop Location</title>
<style>
body { font-family: Arial, sans-serif; background: #f5f6fa; margin: 0; padding: 30px; }
.location-box { max-width: 620px; margin: auto; background: white; padding: 28px; border-radius: 12px; box-shadow: 0 3px 10px rgba(0,0,0,.08); }
button { padding: 12px 18px; border: 0; border-radius: 6px; background: #ff6b9d; color: white; font-weight: bold; cursor: pointer; }
a { color: #ff4f89; }
#location-status { margin-top: 18px; font-weight: 600; }
</style>
</head>
<body>
<main class="location-box">
<p><a href="dashboard.php">Back to dashboard</a></p>
<h1>Share shop location</h1>
<p><strong>Shop address:</strong> Near JK Hotel, Morbi Road, Rajkot</p>
<p>Keep this page open on the shop device. Customers will see the latest shared position in the Our Location section.</p>
<button id="share-location" type="button">Start sharing shop location</button>
<p id="location-status" aria-live="polite">Location is not being shared.</p>
</main>
<script>
const button = document.getElementById('share-location');
const status = document.getElementById('location-status');
let watchId = null;

button.addEventListener('click', () => {
    if (watchId !== null) {
        navigator.geolocation.clearWatch(watchId);
        watchId = null;
        button.textContent = 'Start sharing shop location';
        status.textContent = 'Location sharing stopped.';
        return;
    }

    if (!navigator.geolocation) {
        status.textContent = 'This browser does not support location sharing.';
        return;
    }
    button.disabled = true;
    status.textContent = 'Waiting for location permission...';
    watchId = navigator.geolocation.watchPosition(async position => {
        const body = new URLSearchParams({
            latitude: position.coords.latitude,
            longitude: position.coords.longitude
        });
        try {
            const response = await fetch('../../server/Admin/update_shop_location.php', { method: 'POST', body });
            if (!response.ok) throw new Error();
            status.textContent = 'Shop location is live.';
            button.textContent = 'Stop sharing shop location';
            button.disabled = false;
        } catch {
            status.textContent = 'Could not update shop location. Check the server connection.';
            button.disabled = false;
        }
    }, () => {
        watchId = null;
        button.disabled = false;
        status.textContent = 'Location permission was denied.';
    }, { enableHighAccuracy: true, maximumAge: 5000, timeout: 10000 });
});
</script>
</body>
</html>