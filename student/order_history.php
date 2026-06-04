<?php
require "../api/session.php";
requireRole('student');
require "../api/db.php";

$stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id=? ORDER BY created_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$orders = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
<title>Order History</title>
<link rel="stylesheet" href="../assets/css/main.css">
<link rel="stylesheet" href="../assets/css/student.css">
<link rel="stylesheet" href="../assets/css/dashboard.css">
<link rel="stylesheet" href="../assets/css/schedule.css">
</head>
<body>

<?php include "../includes/navbar.php"; ?>

<div class="dashboard-container">

<?php include "../includes/sidebar_student.php"; ?>

<div class="container">
<h2>Order History</h2>

<div class="orders-grid">

<?php if(empty($orders)): ?>
<div class="empty-orders">
  <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V19.5a2.25 2.25 0 002.25 2.25h.75"/></svg>
  <strong>No orders yet</strong>
  Start ordering from the menu and your history will show up here.
</div>

<?php else: ?>

<?php foreach($orders as $row): ?>
<div class="order-card">

  <div class="order-card-header">
    <div class="order-id">Order <span>#<?= $row['order_id'] ?></span></div>
    <div class="order-status <?= strtolower($row['status']) ?>"><?= ucfirst($row['status']) ?></div>
  </div>

  <div class="order-card-body">
    <div class="order-detail-row">
      <span class="order-detail-label">Type</span>
      <span class="order-type-badge <?= $row['order_type'] ?>"><?= ucfirst($row['order_type']) ?></span>
    </div>
    <div class="order-detail-row">
      <span class="order-detail-label">Date</span>
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