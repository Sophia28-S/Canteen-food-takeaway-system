<nav class="top-nav">
    <div class="logo">🍔 Canteen Takeaway</div>

    <div class="nav-right">
        <span class="role"><?= ucfirst($_SESSION['role']) ?></span>
        <span class="user"><?= htmlspecialchars($_SESSION['name']) ?></span>
        <a href="../auth/logout.php" class="logout">Logout</a>
    </div>
</nav>
