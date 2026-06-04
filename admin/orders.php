<?php
require "../api/session.php";
requireRole('admin');
require "../api/db.php";

$orders = $pdo->query("
    SELECT o.*, u.name 
    FROM orders o
    JOIN users u ON o.user_id=u.user_id
    ORDER BY o.created_at DESC
")->fetchAll();

$totalRevenue = array_sum(array_column(array_filter($orders, fn($o)=>$o['status']==='completed'), 'total_amount'));
$byStatus = array_count_values(array_column($orders,'status'));
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>All Orders</title>
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
            <h1 class="admin-page-title">All Orders</h1>
            <p class="admin-page-sub">Complete order history across all students</p>
        </div>
        <span style="font-family:'Sora',sans-serif;font-size:18px;font-weight:700;color:var(--primary);">₹<?= number_format($totalRevenue,2) ?> <span style="font-size:12px;font-weight:500;color:var(--text-muted);">revenue</span></span>
    </div>

    <!-- MINI STATS -->
    <div style="display:flex;gap:10px;flex-wrap:wrap;">
        <?php $statusLabels=['pending'=>'Pending','accepted'=>'Accepted','preparing'=>'Preparing','ready'=>'Ready','completed'=>'Completed','cancelled'=>'Cancelled'];
        foreach($statusLabels as $s=>$label): ?>
        <div style="background:var(--surface);border:1.5px solid var(--border-soft);border-radius:var(--radius-md);padding:10px 16px;box-shadow:var(--shadow-sm);display:flex;flex-direction:column;align-items:center;min-width:80px;">
            <span style="font-family:'Sora',sans-serif;font-size:20px;font-weight:700;color:var(--text-main);"><?= $byStatus[$s]??0 ?></span>
            <span class="status-pill <?= $s ?>" style="margin-top:4px;"><?= $label ?></span>
        </div>
        <?php endforeach; ?>
    </div>

    <div class="admin-panel">
        <div class="admin-panel-head">
            <h3>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007z"/></svg>
                Order History
            </h3>
            <span style="font-size:13px;color:var(--text-muted);"><?= count($orders) ?> orders</span>
        </div>
        <div class="admin-panel-body" style="padding:0;">
            <?php if(empty($orders)): ?>
            <div class="admin-empty"><span>No orders yet</span></div>
            <?php else: ?>
            <div class="data-list">
            <?php foreach($orders as $o): ?>
            <div class="data-row">
                <div class="data-row-left">
                    <div class="data-row-title">#<?= $o['order_id'] ?> — <?= htmlspecialchars($o['name']) ?></div>
                    <div class="data-row-sub"><?= date('d M Y, h:i A', strtotime($o['created_at'])) ?> · <?= ucfirst($o['order_type']) ?> · <?= ucfirst($o['payment_method']) ?></div>
                </div>
                <div class="data-row-right">
                    <span class="status-pill <?= $o['status'] ?>"><?= ucfirst($o['status']) ?></span>
                    <span style="font-family:'Sora',sans-serif;font-weight:700;font-size:14px;color:var(--text-main);">₹<?= number_format($o['total_amount'],2) ?></span>
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