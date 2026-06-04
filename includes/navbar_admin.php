<nav class="top-nav">
    <div class="logo">🛠️ Canteen Admin Panel</div>

    <div class="nav-right">
        <span class="role admin-role">
            <?= ucfirst($_SESSION['role']) ?>
        </span>

        <span class="user">
            <?= htmlspecialchars($_SESSION['name']) ?>
        </span>

        <a href="../auth/logout.php" class="logout">Logout</a>
    </div>
</nav>
