<?php
require "../api/session.php";
requireRole('staff');
require "../api/db.php";

/* NEW ORDERS */
$newOrders = $pdo->query("
    SELECT COUNT(*) 
    FROM orders 
    WHERE status = 'pending'
")->fetchColumn();

/* ACCEPTED */
$accepted = $pdo->query("
    SELECT COUNT(*) 
    FROM orders 
    WHERE status = 'accepted'
")->fetchColumn();

/* PREPARING */
$preparing = $pdo->query("
    SELECT COUNT(*) 
    FROM orders 
    WHERE status = 'preparing'
")->fetchColumn();

/* READY */
$ready = $pdo->query("
    SELECT COUNT(*) 
    FROM orders 
    WHERE status = 'ready'
")->fetchColumn();

/* TODAY REVENUE */
$stmt = $pdo->query("
    SELECT SUM(total_amount) 
    FROM orders 
    WHERE DATE(created_at) = CURDATE()
");
$todayRevenue = $stmt->fetchColumn() ?: 0;
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Staff Dashboard</title>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/main.css">
<link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body>

<?php include "../includes/navbar_staff.php"; ?>

<div class="dashboard-container">

<?php include "../includes/sidebar_staff.php"; ?>

<main class="main-content">

<h1>Kitchen Dashboard 👨‍🍳</h1>
<p class="muted">Manage today’s orders</p>

<div class="cards">
    <div class="card">
        <h3>🆕 New Orders</h3>
        <p class="big"><?= $newOrders ?></p>
    </div>

    <div class="card">
        <h3>📋 Accepted</h3>
        <p class="big"><?= $accepted ?></p>
    </div>

    <div class="card">
        <h3>🔥 Preparing</h3>
        <p class="big"><?= $preparing ?></p>
    </div>

    <div class="card">
        <h3>✅ Ready</h3>
        <p class="big"><?= $ready ?></p>
    </div>

    <div class="card">
        <h3>💰 Today Revenue</h3>
        <p class="big">₹<?= number_format($todayRevenue, 2) ?></p>
    </div>
</div>

</main>
</div>
</body>
</html>
