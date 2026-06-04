<?php
require "../api/session.php";
requireRole('admin');
require "../api/db.php";

$totalUsers    = $pdo->query("SELECT COUNT(*) FROM users")->fetchColumn();
$totalStudents = $pdo->query("SELECT COUNT(*) FROM users WHERE role='student'")->fetchColumn();
$totalStaff    = $pdo->query("SELECT COUNT(*) FROM users WHERE role='staff'")->fetchColumn();
$totalOrders   = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();
$totalRevenue  = $pdo->query("SELECT SUM(total_amount) FROM orders WHERE status='completed'")->fetchColumn() ?: 0;
$pendingOrders = $pdo->query("SELECT COUNT(*) FROM orders WHERE status='pending'")->fetchColumn();
$todayOrders   = $pdo->query("SELECT COUNT(*) FROM orders WHERE DATE(created_at)=CURDATE()")->fetchColumn();
$topItem       = $pdo->query("SELECT m.item_name FROM order_items oi JOIN menu_items m ON oi.item_id=m.item_id GROUP BY m.item_id ORDER BY SUM(oi.quantity) DESC LIMIT 1")->fetchColumn() ?: "N/A";

/* Recent 5 orders */
$recentOrders = $pdo->query("SELECT o.*, u.name FROM orders o JOIN users u ON o.user_id=u.user_id ORDER BY o.created_at DESC LIMIT 5")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Admin Dashboard</title>
<link rel="stylesheet" href="../assets/css/main.css">
<link rel="stylesheet" href="../assets/css/dashboard.css">
<link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
<?php include "../includes/navbar_admin.php"; ?>
<div class="dashboard-container">
<?php include "../includes/sidebar_admin.php"; ?>
<main class="main-content">

    <div class="admin-page-header">
        <div>
            <h1 class="admin-page-title">Admin Dashboard</h1>
            <p class="admin-page-sub">System overview and live stats</p>
        </div>
    </div>

    <!-- STAT CARDS -->
    <div class="admin-cards">

        <div class="admin-card">
            <div class="admin-card-icon" style="background:#fff7ed;color:#f97316;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
            </div>
            <h3>Total Users</h3>
            <p class="big"><?= $totalUsers ?></p>
            <p class="sub"><?= $totalStudents ?> students · <?= $totalStaff ?> staff</p>
        </div>

        <div class="admin-card">
            <div class="admin-card-icon" style="background:#f0fdf4;color:#22c55e;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h3>Total Revenue</h3>
            <p class="big">₹<?= number_format($totalRevenue, 0) ?></p>
            <p class="sub">from completed orders</p>
        </div>

        <div class="admin-card">
            <div class="admin-card-icon" style="background:#eff6ff;color:#3b82f6;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007z"/></svg>
            </div>
            <h3>Total Orders</h3>
            <p class="big"><?= $totalOrders ?></p>
            <p class="sub"><?= $todayOrders ?> today</p>
        </div>

        <div class="admin-card">
            <div class="admin-card-icon" style="background:#fffbeb;color:#f59e0b;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h3>Pending Orders</h3>
            <p class="big"><?= $pendingOrders ?></p>
            <p class="sub">awaiting staff action</p>
        </div>

        <div class="admin-card">
            <div class="admin-card-icon" style="background:#f5f3ff;color:#8b5cf6;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.562.562 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/></svg>
            </div>
            <h3>Top Item</h3>
            <p class="big" style="font-size:16px;"><?= htmlspecialchars($topItem) ?></p>
            <p class="sub">most ordered</p>
        </div>

        <div class="admin-card">
            <div class="admin-card-icon" style="background:#fdf4ff;color:#a855f7;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/></svg>
            </div>
            <h3>Staff Members</h3>
            <p class="big"><?= $totalStaff ?></p>
            <p class="sub">active staff accounts</p>
        </div>

    </div>

    <!-- RECENT ORDERS -->
    <div class="admin-panel">
        <div class="admin-panel-head">
            <h3>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z"/></svg>
                Recent Orders
            </h3>
            <a href="orders.php" class="btn-sm btn-ghost">View All →</a>
        </div>
        <div class="admin-panel-body" style="padding:0;">
            <?php if(empty($recentOrders)): ?>
            <div class="admin-empty"><span>No orders yet</span></div>
            <?php else: ?>
            <div class="data-list">
            <?php foreach($recentOrders as $o): ?>
            <div class="data-row">
                <div class="data-row-left">
                    <div class="data-row-title">#<?= $o['order_id'] ?> — <?= htmlspecialchars($o['name']) ?></div>
                    <div class="data-row-sub"><?= date('d M Y, h:i A', strtotime($o['created_at'])) ?> · <?= ucfirst($o['order_type']) ?></div>
                </div>
                <div class="data-row-right">
                    <span class="status-pill <?= $o['status'] ?>"><?= ucfirst($o['status']) ?></span>
                    <span style="font-family:'Sora',sans-serif;font-weight:700;font-size:14px;">₹<?= number_format($o['total_amount'],2) ?></span>
                </div>
            </div>
            <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- QUICK LINKS -->
    <div class="admin-panel">
        <div class="admin-panel-head">
            <h3>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/></svg>
                Quick Actions
            </h3>
        </div>
        <div class="admin-panel-body">
            <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(160px,1fr));gap:10px;">
                <a href="manage_users.php" class="btn-sm btn-blue" style="padding:12px 16px;font-size:13px;">👥 Manage Users</a>
                <a href="manage_categories.php" class="btn-sm btn-orange" style="padding:12px 16px;font-size:13px;">🏷️ Categories</a>
                <a href="manage_slots.php" class="btn-sm btn-success" style="padding:12px 16px;font-size:13px;">⏰ Pickup Slots</a>
                <a href="orders.php" class="btn-sm btn-ghost" style="padding:12px 16px;font-size:13px;">📦 All Orders</a>
                <a href="reports.php" class="btn-sm btn-ghost" style="padding:12px 16px;font-size:13px;">📊 Reports</a>
                <a href="settings.php" class="btn-sm btn-ghost" style="padding:12px 16px;font-size:13px;">⚙️ Settings</a>
            </div>
        </div>
    </div>

</main>
</div>
</body>
</html>