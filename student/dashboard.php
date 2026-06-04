<?php
require "../api/session.php";
requireRole('student');
require "../api/db.php";

$userId = $_SESSION['user_id'];

/* ACTIVE ORDERS COUNT */
$stmt = $pdo->prepare("
    SELECT COUNT(*) 
    FROM orders 
    WHERE user_id = ? 
    AND status IN ('pending','confirmed','preparing','ready')
");
$stmt->execute([$userId]);
$activeOrders = $stmt->fetchColumn();

/* TOTAL ORDERS */
$stmt = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE user_id = ?");
$stmt->execute([$userId]);
$totalOrders = $stmt->fetchColumn();

/* FAVORITE ITEM */
$stmt = $pdo->prepare("
    SELECT m.item_name
    FROM order_items oi
    JOIN menu_items m ON oi.item_id = m.item_id
    JOIN orders o ON oi.order_id = o.order_id
    WHERE o.user_id = ?
    GROUP BY m.item_id
    ORDER BY SUM(oi.quantity) DESC
    LIMIT 1
");
$stmt->execute([$userId]);
$favItem = $stmt->fetchColumn() ?: "N/A";

/* ACTIVE ORDER (most recent) for status tracker */
$stmt = $pdo->prepare("
    SELECT * FROM orders
    WHERE user_id = ?
    AND status IN ('pending','confirmed','preparing','ready')
    ORDER BY created_at DESC
    LIMIT 1
");
$stmt->execute([$userId]);
$activeOrder = $stmt->fetch();

/* RECENT ORDERS (last 3) */
$stmt = $pdo->prepare("
    SELECT * FROM orders
    WHERE user_id = ?
    ORDER BY created_at DESC
    LIMIT 3
");
$stmt->execute([$userId]);
$recentOrders = $stmt->fetchAll();

/* TODAY'S MENU HIGHLIGHTS (4 random available items) */
$stmt = $pdo->prepare("
    SELECT m.*, c.category_name
    FROM menu_items m
    JOIN categories c ON m.category_id = c.category_id
    WHERE m.availability = 1 AND m.stock > 0
    ORDER BY RAND()
    LIMIT 4
");
$stmt->execute();
$menuHighlights = $stmt->fetchAll();

/* SYSTEM SETTINGS for currency */
$stmt = $pdo->prepare("SELECT * FROM system_settings WHERE id=1");
$stmt->execute();
$settings = $stmt->fetch();
$currency = $settings['currency_symbol'] ?? '₹';

/* STATUS STEPS */
$statusSteps = ['pending', 'confirmed', 'preparing', 'ready'];
$statusLabels = ['Placed', 'Confirmed', 'Preparing', 'Ready'];
$currentStep = $activeOrder ? array_search($activeOrder['status'], $statusSteps) : -1;
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Student Dashboard</title>
<link rel="stylesheet" href="../assets/css/main.css">
<link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body>

<?php include "../includes/navbar.php"; ?>

<div class="dashboard-container">

<?php include "../includes/sidebar_student.php"; ?>

<main class="main-content">

    <!-- ===================== WELCOME ===================== -->
    <div class="welcome-row">
        <div>
            <h1>Welcome back, <?= htmlspecialchars($_SESSION['name']) ?> 👋</h1>
            <p class="muted">Here's what's happening at the canteen today</p>
        </div>
        <a href="menu.php" class="order-now-btn">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            New Order
        </a>
    </div>

    <!-- ===================== STAT CARDS ===================== -->
    <div class="cards">
        <div class="card">
            <div class="card-icon" style="background:#fff7ed;color:#f97316;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007z"/></svg>
            </div>
            <h3>Active Orders</h3>
            <p class="big"><?= $activeOrders ?></p>
            <span class="card-sub">currently in progress</span>
        </div>

        <div class="card">
            <div class="card-icon" style="background:#f0fdf4;color:#22c55e;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            </div>
            <h3>Total Orders</h3>
            <p class="big"><?= $totalOrders ?></p>
            <span class="card-sub">all time</span>
        </div>

        <div class="card">
            <div class="card-icon" style="background:#fdf4ff;color:#a855f7;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.48 3.499a.562.562 0 011.04 0l2.125 5.111a.563.563 0 00.475.345l5.518.442c.499.04.701.663.321.988l-4.204 3.602a.563.563 0 00-.182.557l1.285 5.385a.562.562 0 01-.84.61l-4.725-2.885a.563.563 0 00-.586 0L6.982 20.54a.562.562 0 01-.84-.61l1.285-5.386a.562.562 0 00-.182-.557l-4.204-3.602a.562.562 0 01.321-.988l5.518-.442a.563.563 0 00.475-.345L11.48 3.5z"/></svg>
            </div>
            <h3>Favourite Item</h3>
            <p class="big fav-text"><?= htmlspecialchars($favItem) ?></p>
            <span class="card-sub">most ordered</span>
        </div>
    </div>

    <!-- ===================== ACTIVE ORDER TRACKER ===================== -->
    <?php if($activeOrder): ?>
    <div class="section-block">
        <div class="section-header">
            <h2>Active Order</h2>
            <a href="order_history.php" class="section-link">View all →</a>
        </div>

        <div class="tracker-card">
            <div class="tracker-meta">
                <div class="tracker-id">Order <span>#<?= $activeOrder['order_id'] ?></span></div>
                <div class="tracker-amount"><?= $currency ?><?= number_format($activeOrder['total_amount'], 2) ?></div>
            </div>

            <div class="tracker-steps">
                <?php foreach($statusSteps as $i => $step): ?>
                <div class="tracker-step <?= $i <= $currentStep ? 'done' : '' ?> <?= $i == $currentStep ? 'active' : '' ?>">
                    <div class="step-dot">
                        <?php if($i < $currentStep): ?>
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                        <?php elseif($i == $currentStep): ?>
                        <div class="step-pulse"></div>
                        <?php endif; ?>
                    </div>
                    <?php if($i < count($statusSteps) - 1): ?>
                    <div class="step-line <?= $i < $currentStep ? 'done' : '' ?>"></div>
                    <?php endif; ?>
                    <div class="step-label"><?= $statusLabels[$i] ?></div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- ===================== BOTTOM ROW ===================== -->
    <div class="bottom-row">

        <!-- RECENT ORDERS -->
        <div class="section-block flex-1">
            <div class="section-header">
                <h2>Recent Orders</h2>
                <a href="order_history.php" class="section-link">See all →</a>
            </div>

            <?php if(empty($recentOrders)): ?>
            <div class="empty-panel">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z"/></svg>
                No orders yet
            </div>
            <?php else: ?>
            <div class="recent-list">
                <?php foreach($recentOrders as $o): ?>
                <div class="recent-item">
                    <div class="recent-left">
                        <div class="recent-id">#<?= $o['order_id'] ?></div>
                        <div class="recent-date"><?= date('d M, h:i A', strtotime($o['created_at'])) ?></div>
                    </div>
                    <div class="recent-right">
                        <div class="order-status-badge <?= strtolower($o['status']) ?>"><?= ucfirst($o['status']) ?></div>
                        <div class="recent-amount"><?= $currency ?><?= number_format($o['total_amount'], 2) ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <!-- RIGHT COLUMN: Quick Actions + Menu Highlights -->
        <div class="right-col">

            <!-- QUICK ACTIONS -->
            <div class="section-block">
                <div class="section-header"><h2>Quick Actions</h2></div>
                <div class="quick-actions">
                    <a href="menu.php" class="quick-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/></svg>
                        Browse Menu
                    </a>
                    <a href="cart.php" class="quick-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z"/></svg>
                        My Cart
                    </a>
                    <a href="schedule.php" class="quick-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
                        Schedule
                    </a>
                    <a href="order_history.php" class="quick-btn">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V19.5a2.25 2.25 0 002.25 2.25h.75"/></svg>
                        My Orders
                    </a>
                </div>
            </div>

            <!-- TODAY'S MENU HIGHLIGHTS -->
            <div class="section-block">
                <div class="section-header">
                    <h2>On the Menu Today</h2>
                    <a href="menu.php" class="section-link">See all →</a>
                </div>

                <?php if(empty($menuHighlights)): ?>
                <div class="empty-panel">Nothing available right now</div>
                <?php else: ?>
                <div class="highlights-list">
                    <?php foreach($menuHighlights as $m): ?>
                    <div class="highlight-item">
                        <div class="highlight-info">
                            <div class="highlight-name"><?= htmlspecialchars($m['item_name']) ?></div>
                            <div class="highlight-cat"><?= htmlspecialchars($m['category_name']) ?></div>
                        </div>
                        <div class="highlight-right">
                            <div class="highlight-price"><?= $currency ?><?= number_format($m['price'], 2) ?></div>
                            <form method="POST" action="cart.php">
                                <input type="hidden" name="item_id" value="<?= $m['item_id'] ?>">
                                <input type="hidden" name="quantity" value="1">
                                <button type="submit" name="add_cart" class="highlight-add">+</button>
                            </form>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

        </div>
    </div>

</main>
</div>
</body>
</html>