

<aside class="sidebar">
<?php $current = basename($_SERVER['PHP_SELF']); ?>
    <a class="<?= $current == 'dashboard.php' ? 'active' : '' ?>" href="dashboard.php">🏠 Dashboard</a>
    <a class="<?= $current == 'menu.php' ? 'active' : '' ?>" href="menu.php">🍽️ Menu</a>
    <a class="<?= $current == 'orders.php' ? 'active' : '' ?>" href="orders.php">🛒 Orders</a>
    <a class="<?= $current == 'today_orders.php' ? 'active' : '' ?>" href="today_orders.php">⏰ Schedule Pickup</a>
    <a class="<?= $current == 'pickup_queue.php' ? 'active' : '' ?>" href="pickup_queue.php">📦 My Orders</a>
    <a class="<?= $current == 'profile.php' ? 'active' : '' ?>" href="profile.php">👤 Profile</a>
</aside>