<?php
require "../api/session.php";
requireRole('staff');
require "../api/db.php";

$stmt = $pdo->prepare("
    SELECT o.*, u.name
    FROM orders o
    JOIN users u ON o.user_id = u.user_id
    WHERE DATE(o.created_at) = CURDATE()
    ORDER BY o.created_at DESC
");
$stmt->execute();
$orders = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
<title>Today's Orders</title>
<link rel="stylesheet" href="../assets/css/main.css">
<link rel="stylesheet" href="../assets/css/dashboard.css">
<link rel="stylesheet" href="../assets/css/staff_orders.css">
</head>

<body>
<?php include "../includes/navbar_staff.php"; ?>
<div class="dashboard-container">
<?php include "../includes/sidebar_staff.php"; ?>

<main class="main-content">
<h2>Today's Orders</h2>

<div class="table-card">
<table class="orders-table">
<tr>
<th>ID</th>
<th>Customer</th>
<th>Total</th>
<th>Status</th>
</tr>

<?php foreach($orders as $order): ?>
<tr>
<td>#<?= $order['order_id'] ?></td>
<td><?= htmlspecialchars($order['name']) ?></td>
<td>₹<?= $order['total_amount'] ?></td>
<td>
<span class="status-badge <?= $order['status'] ?>">
<?= $order['status'] ?>
</span>
</td>
</tr>
<?php endforeach; ?>

</table>
</div>
</main>
</div>
</body>
</html>
