<?php
require "../api/session.php";
requireRole('student');
require "../api/db.php";

/* GET SYSTEM SETTINGS */
$stmt = $pdo->prepare("SELECT * FROM system_settings WHERE id=1");
$stmt->execute();
$settings = $stmt->fetch();

if (!$settings['ordering_enabled']) {
    die("<h2 style='text-align:center;margin-top:50px;'>Ordering is currently disabled.</h2>");
}

/* GET MENU ITEMS */
$stmt = $pdo->prepare("
    SELECT m.*, c.category_name 
    FROM menu_items m
    JOIN categories c ON m.category_id = c.category_id
    WHERE m.availability = 1 AND m.stock > 0
    ORDER BY c.category_name
");
$stmt->execute();
$items = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html>
<head>
<title>Menu</title>
<link rel="stylesheet" href="../assets/css/main.css">
<link rel="stylesheet" href="../assets/css/student.css">
<link rel="stylesheet" href="../assets/css/dashboard.css">
<link rel="stylesheet" href="../assets/css/menu.css">
</head>
<body>

<?php include "../includes/navbar.php"; ?>

<div class="dashboard-container">

<?php include "../includes/sidebar_student.php"; ?>

<div class="container">
<h2>Menu</h2>

<div class="menu-grid">
<?php foreach($items as $index => $row): ?>
<div class="card" style="animation-delay: <?= $index * 0.06 ?>s">

    <!-- ITEM IMAGE -->
    <div class="menu-img-wrap">
        <?php if(!empty($row['image'])): ?>
            <img
                src="../uploads/<?= htmlspecialchars($row['image']) ?>"
                alt="<?= htmlspecialchars($row['item_name']) ?>"
                class="menu-img"
                onerror="this.parentElement.classList.add('no-img')"
            >
        <?php else: ?>
            <div class="menu-img-placeholder">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.4" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8.25v-1.5m0 1.5c-1.355 0-2.697.056-4.024.166C6.845 8.51 6 9.473 6 10.608v2.513m6-4.871c1.355 0 2.697.056 4.024.166C17.155 8.51 18 9.473 18 10.608v2.513M15 21h3.75M15 21H9m6 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.375a1.125 1.125 0 00-1.125 1.125V21M9 21H5.25M9 21v-3.375c0-.621.504-1.125 1.125-1.125h.375c.621 0 1.125.504 1.125 1.125V21m0 0h6"/>
                </svg>
            </div>
        <?php endif; ?>

        <!-- Category chip overlaid on image -->
        <span class="menu-category-chip"><?= htmlspecialchars($row['category_name']) ?></span>
    </div>

    <!-- CARD BODY -->
    <div class="menu-card-body">
        <h3><?= htmlspecialchars($row['item_name']) ?></h3>

        <?php if(!empty($row['description'])): ?>
        <p class="menu-desc"><?= htmlspecialchars($row['description']) ?></p>
        <?php endif; ?>

        <div class="menu-meta">
            <span class="menu-price"><?= $settings['currency_symbol'] ?><?= number_format($row['price'], 2) ?></span>
            <span class="menu-stock">
                <span class="stock-dot"></span>
                <?= $row['stock'] ?> left
            </span>
        </div>
    </div>

    <!-- ADD TO CART FORM -->
    <form method="POST" action="cart.php" class="menu-form">
        <input type="hidden" name="item_id" value="<?= $row['item_id'] ?>">
        <input type="number" name="quantity" min="1" max="<?= $row['stock'] ?>" value="1">
        <button type="submit" name="add_cart">Add to Cart</button>
    </form>

</div>
<?php endforeach; ?>
</div>

</div><!-- end container -->
</div><!-- end dashboard-container -->

</body>
</html>