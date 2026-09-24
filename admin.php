<?php
include __DIR__ . '/config/db.php';

$result = mysqli_query($conn, "SELECT * FROM online_orders");

echo "Rows Found: " . mysqli_num_rows($result);
?>
<table border="1">
<tr>
    <th>ID</th>
    <th>Name</th>
    <th>Phone</th>
    <th>Address</th>
    <th>Partner</th>
    <th>Total</th>
</tr>

<?php while($row = mysqli_fetch_assoc($result)) { ?>
<tr>
    <td><?php echo $row['id']; ?></td>
    <td><?php echo htmlspecialchars($row['customer_name']); ?></td>
    <td><?php echo htmlspecialchars($row['phone']); ?></td>
    <td><?php echo htmlspecialchars($row['address']); ?></td>
    <td><?php echo htmlspecialchars($row['delivery_partner']); ?></td>
    <td><?php echo htmlspecialchars($row['total']); ?></td>
</tr>
<?php } ?>
</table>





