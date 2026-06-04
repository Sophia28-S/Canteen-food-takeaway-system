<aside class="sidebar">
<?php $current = basename($_SERVER['PHP_SELF']); ?>
    <a class="<?= $current == 'dashboard.php' ? 'active' : '' ?>" href="dashboard.php">🏠 Dashboard</a>
    <a class="<?= $current == 'menu.php' ? 'active' : '' ?>" href="menu.php">🍽️ Menu</a>
    <a class="<?= $current == 'cart.php' ? 'active' : '' ?>" href="cart.php">🛒 My Cart</a>
    <a class="<?= $current == 'schedule.php' ? 'active' : '' ?>" href="schedule.php">⏰ Schedule Pickup</a>
    <a class="<?= $current == 'order_history.php' ? 'active' : '' ?>" href="order_history.php">📦 My Orders</a>
    <a class="<?= $current == 'profile.php' ? 'active' : '' ?>" href="profile.php">👤 Profile</a>
</aside>