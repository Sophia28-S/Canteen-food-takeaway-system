<?php
require "../api/session.php";
requireRole('student');
require "../api/db.php";

$stmt = $pdo->prepare("
    SELECT o.*, t.slot_time
    FROM orders o
    LEFT JOIN pickup_slots t ON o.slot_id=t.slot_id
    WHERE o.user_id=? AND o.order_type='scheduled'
    ORDER BY o.created_at DESC
");
$stmt->execute([$_SESSION['user_id']]);
$orders = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
<title>Scheduled Orders</title>
<link rel="stylesheet" href="../assets/css/main.css">
<link rel="stylesheet" href="../assets/css/student.css">
<link rel="stylesheet" href="../assets/css/dashboard.css">
<link rel="stylesheet" href="../assets/css/pickup.css">
</head>
<body>

<?php include "../includes/navbar.php"; ?>

<div class="dashboard-container">

<?php include "../includes/sidebar_student.php"; ?>

<div class="container">
<h2>Schedule Pickup</h2>

<div class="orders-grid">

<?php if(empty($orders)): ?>

<div class="empty-orders">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.4" stroke="currentColor">
        <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/>
    </svg>
    <strong>No scheduled pickups yet</strong>
    <span>Place a scheduled order from the cart and it will appear here.</span>
    <a href="menu.php" class="empty-cta">Browse Menu</a>
</div>

<?php else: ?>

<?php foreach($orders as $index => $row): ?>
<div class="order-card" style="animation-delay: <?= $index * 0.06 ?>s">

    <div class="order-card-header">
        <div class="order-id">Order <span>#<?= $row['order_id'] ?></span></div>
        <div class="order-status <?= strtolower($row['status']) ?>"><?= ucfirst($row['status']) ?></div>
    </div>

    <div class="order-card-body">

<div class="order-detail-row">
    <span class="order-detail-label">Pickup Slot</span>

    <?php if($row['slot_time']): ?>
    <div class="slot-chip">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
        </svg>
        <?= htmlspecialchars($row['slot_time']) ?>
    </div>
    <?php else: ?>
    <span class="order-detail-value">—</span>
    <?php endif; ?>

</div>

        <div class="order-detail-row">
            <span class="order-detail-label">Placed On</span>
            <span class="order-detail-value"><?= date('d M Y, h:i A', strtotime($row['created_at'])) ?></span>
        </div>

    </div>

    <div class="order-card-footer">
        <span class="order-total-label">Total</span>
        <span class="order-total-amount">₹<?= number_format($row['total_amount'], 2) ?></span>
    </div>

</div>
<?php endforeach; ?>

<?php endif; ?>

</div><!-- end orders-grid -->
</div><!-- end container -->
</div><!-- end dashboard-container -->

</body>
</html>