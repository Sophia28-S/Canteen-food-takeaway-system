<?php
require "../api/session.php";
requireRole('admin');
require "../api/db.php";

$totalRevenue   = $pdo->query("SELECT SUM(total_amount) FROM orders WHERE status='completed'")->fetchColumn() ?: 0;
$totalOrders    = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$completedOrders= $pdo->query("SELECT COUNT(*) FROM orders WHERE status='completed'")->fetchColumn();
$cancelledOrders= $pdo->query("SELECT COUNT(*) FROM orders WHERE status='cancelled'")->fetchColumn();
$todayRevenue   = $pdo->query("SELECT SUM(total_amount) FROM orders WHERE status='completed' AND DATE(created_at)=CURDATE()")->fetchColumn() ?: 0;
$todayOrders    = $pdo->query("SELECT COUNT(*) FROM orders WHERE DATE(created_at)=CURDATE()")->fetchColumn();

$topItems = $pdo->query("
    SELECT m.item_name, SUM(oi.quantity) as total, SUM(oi.quantity*oi.price) as revenue
    FROM order_items oi
    JOIN menu_items m ON oi.item_id=m.item_id
    GROUP BY m.item_id
    ORDER BY total DESC
    LIMIT 5
")->fetchAll();

$revenueByDay = $pdo->query("
    SELECT DATE(created_at) as day, SUM(total_amount) as rev, COUNT(*) as cnt
    FROM orders
    WHERE status='completed' AND created_at >= NOW() - INTERVAL 7 DAY
    GROUP BY DATE(created_at)
    ORDER BY day ASC
")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Reports</title>
<link rel="stylesheet" href="../assets/css/main.css">
<link rel="stylesheet" href="../assets/css/dashboard.css">
<link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body class="admin-theme">
<?php include "../includes/navbar_admin.php"; ?>
<div class="dashboard-container">
<?php include "../includes/sidebar_admin.php"; ?>
<main class="main-content">

    <div class="admin-page-header">
        <div>
            <h1 class="admin-page-title">Reports & Analytics</h1>
            <p class="admin-page-sub">Sales performance and top items overview</p>
        </div>
    </div>

    <!-- STAT CARDS -->
    <div class="admin-cards">
        <div class="admin-card">
            <div class="admin-card-icon" style="background:#f0fdf4;color:#22c55e;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z"/></svg>
            </div>
            <h3>Total Revenue</h3>
            <p class="big">₹<?= number_format($totalRevenue,0) ?></p>
            <p class="sub">all completed orders</p>
        </div>
        <div class="admin-card">
            <div class="admin-card-icon" style="background:#fff7ed;color:#f97316;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
            </div>
            <h3>Today's Revenue</h3>
            <p class="big">₹<?= number_format($todayRevenue,0) ?></p>
            <p class="sub"><?= $todayOrders ?> orders today</p>
        </div>
        <div class="admin-card">
            <div class="admin-card-icon" style="background:#eff6ff;color:#3b82f6;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007z"/></svg>
            </div>
            <h3>Total Orders</h3>
            <p class="big"><?= $totalOrders ?></p>
            <p class="sub"><?= $completedOrders ?> completed</p>
        </div>
        <div class="admin-card">
            <div class="admin-card-icon" style="background:#fef2f2;color:#ef4444;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </div>
            <h3>Cancelled</h3>
            <p class="big"><?= $cancelledOrders ?></p>
            <p class="sub"><?= $totalOrders>0?round($cancelledOrders/$totalOrders*100,1):0 ?>% cancel rate</p>
        </div>
    </div>

    <!-- TOP 5 ITEMS -->
    <div class="admin-panel">
        <div class="admin-panel-head">
            <h3>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.562.562 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/></svg>
                Top 5 Selling Items
            </h3>
        </div>
        <div class="admin-panel-body">
            <?php if(empty($topItems)): ?>
            <div class="admin-empty"><span>No sales data yet</span></div>
            <?php else: ?>
            <?php $rankClasses=['gold','gold','silver','bronze','',''];
            foreach($topItems as $i=>$item): ?>
            <div class="top-item-row">
                <div class="top-item-rank <?= $rankClasses[$i]??'' ?>"><?= $i+1 ?></div>
                <div class="top-item-name"><?= htmlspecialchars($item['item_name']) ?></div>
                <div style="font-size:13px;color:var(--text-muted);margin-right:16px;"><?= $item['total'] ?> sold</div>
                <div class="top-item-count">₹<?= number_format($item['revenue'],0) ?></div>
            </div>
            <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- LAST 7 DAYS -->
    <div class="admin-panel">
        <div class="admin-panel-head">
            <h3>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/></svg>
                Revenue — Last 7 Days
            </h3>
        </div>
        <div class="admin-panel-body" style="padding:0;">
            <?php if(empty($revenueByDay)): ?>
            <div class="admin-empty"><span>No revenue data for the last 7 days</span></div>
            <?php else: ?>
            <div class="data-list">
            <?php foreach($revenueByDay as $row): ?>
            <div class="data-row">
                <div class="data-row-left">
                    <div class="data-row-title"><?= date('l, d M', strtotime($row['day'])) ?></div>
                    <div class="data-row-sub"><?= $row['cnt'] ?> completed orders</div>
                </div>
                <div class="data-row-right">
                    <span style="font-family:'Sora',sans-serif;font-weight:700;font-size:16px;color:var(--primary);">₹<?= number_format($row['rev'],2) ?></span>
                </div>
            </div>
            <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

</main>
</div>
</body>
</html>