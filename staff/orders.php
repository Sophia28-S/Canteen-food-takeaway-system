<?php
require "../api/session.php";
requireRole('staff');
require "../api/db.php";

/* FETCH ALL ACTIVE ORDERS */
$stmt = $pdo->query("
    SELECT o.*, u.name 
    FROM orders o
    JOIN users u ON o.user_id = u.user_id
    WHERE o.status != 'completed' AND o.status != 'cancelled'
    ORDER BY o.created_at DESC
");
$orders = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Manage Orders</title>

<link rel="stylesheet" href="../assets/css/dashboard.css">
<link rel="stylesheet" href="../assets/css/staff_orders.css">
<link rel="stylesheet" href="../assets/css/main.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body class="staff-theme">

<?php include "../includes/navbar_staff.php"; ?>
<div class="dashboard-container">
<?php include "../includes/sidebar_staff.php"; ?>

<main class="main-content">

<div class="orders-header">
    <h2>Active Orders</h2>
    <div class="badge" id="orderCount"><?= count($orders) ?></div>
</div>

<div class="orders-grid" id="ordersContainer">
    <?php if(empty($orders)): ?>
    <p style="text-align:center; padding:40px; grid-column:1/-1;">
        No active orders right now 🚫
    </p>
<?php endif; ?>

<?php foreach($orders as $order): ?>

<div class="order-card" data-id="<?= $order['order_id'] ?>">

<div class="order-top">
    <h3>#<?= $order['order_id'] ?></h3>
    <span class="status <?= $order['status'] ?>">
        <?= ucfirst($order['status']) ?>
    </span>
</div>

<p><strong>Student:</strong> <?= htmlspecialchars($order['name']) ?></p>
<p><strong>Total:</strong> ₹<?= $order['total_amount'] ?></p>
<p><strong>Type:</strong> <?= ucfirst($order['order_type']) ?></p>

<div class="order-actions">

<button class="btn viewBtn" data-id="<?= $order['order_id'] ?>">
View
</button>

<?php if($order['status'] == 'pending'): ?>
<button class="btn acceptBtn" data-id="<?= $order['order_id'] ?>">Accept</button>
<button class="btn cancelBtn" data-id="<?= $order['order_id'] ?>">Cancel</button>
<?php endif; ?>

<?php if($order['status'] == 'accepted'): ?>
<button class="btn prepBtn" data-id="<?= $order['order_id'] ?>">Preparing</button>
<?php endif; ?>

<?php if($order['status'] == 'preparing'): ?>
<button class="btn readyBtn" data-id="<?= $order['order_id'] ?>">Mark Ready</button>
<?php endif; ?>

<?php if($order['status'] == 'ready'): ?>
<button class="btn completeBtn" data-id="<?= $order['order_id'] ?>">Complete</button>
<button onclick="window.open('print_bill.php?id=<?= $order['order_id'] ?>')"
class="btn printBtn">Print</button>
<?php endif; ?>



</div>
</div>

<?php endforeach; ?>
</div>

</main>
</div>

<!-- MODAL -->
<div class="modal" id="orderModal">
<div class="modal-content">
<span class="close">&times;</span>
<div id="modalBody"></div>
</div>
</div>

<script src="../assets/js/orders.js"></script>

</body>
</html>
