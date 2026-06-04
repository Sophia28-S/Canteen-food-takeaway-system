<?php
require "../api/session.php";
requireRole('staff');
require "../api/db.php";

$stmt = $pdo->prepare("
    SELECT o.*, u.name
    FROM orders o
    JOIN users u ON o.user_id = u.user_id
    WHERE o.status = 'ready'
    ORDER BY o.pickup_time ASC
");
$stmt->execute();
$orders = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
<title>Pickup Queue</title>
<link rel="stylesheet" href="../assets/css/main.css">
<link rel="stylesheet" href="../assets/css/dashboard.css">
<link rel="stylesheet" href="../assets/css/staff_orders.css">
</head>

<body>
<?php include "../includes/navbar_staff.php"; ?>
<div class="dashboard-container">
<?php include "../includes/sidebar_staff.php"; ?>

<main class="main-content">
<h2>Pickup Queue</h2>

<div class="table-card">
<table class="orders-table">
<tr>
<th>ID</th>
<th>Customer</th>
<th>Pickup Time</th>
<th>Total</th>
</tr>

<?php foreach($orders as $order): ?>
<tr>
<td>#<?= $order['order_id'] ?></td>
<td><?= htmlspecialchars($order['name']) ?></td>
<td><?= $order['pickup_time'] ?? 'Immediate' ?></td>
<td>₹<?= $order['total_amount'] ?></td>
</tr>
<?php endforeach; ?>

</table>
</div>
</main>
</div>
</body>
</html>
