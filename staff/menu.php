<?php
require "../api/session.php";
requireRole('staff');
require "../api/db.php";

/* FETCH CATEGORIES */
$catStmt = $pdo->query("SELECT * FROM categories");
$categories = $catStmt->fetchAll();

/* ADD ITEM */
if(isset($_POST['add_item'])){

    $name = trim($_POST['item_name']);
    $desc = trim($_POST['description']);
    $price = $_POST['price'];
    $stock = $_POST['stock'];
    $category = $_POST['category_id'];

    $imageName = null;

    if(!empty($_FILES['image']['name'])){
        $imageName = time() . "_" . $_FILES['image']['name'];
        move_uploaded_file(
            $_FILES['image']['tmp_name'],
            "../uploads/" . $imageName
        );
    }

    $stmt = $pdo->prepare("
        INSERT INTO menu_items
        (category_id,item_name,description,price,image,stock,availability)
        VALUES (?,?,?,?,?,?,?)
    ");

    $availability = $stock > 0 ? 1 : 0;

    $stmt->execute([
        $category,$name,$desc,$price,$imageName,$stock,$availability
    ]);

    header("Location: menu.php");
    exit;
}

/* UPDATE STOCK */
if(isset($_POST['update_stock'])){
    $itemId = $_POST['item_id'];
    $newStock = $_POST['new_stock'];

    $availability = $newStock > 0 ? 1 : 0;

    $stmt = $pdo->prepare("
        UPDATE menu_items 
        SET stock=?, availability=? 
        WHERE item_id=?
    ");
    $stmt->execute([$newStock,$availability,$itemId]);

    header("Location: menu.php");
    exit;
}

/* DELETE */
if(isset($_GET['delete'])){
    $stmt = $pdo->prepare("DELETE FROM menu_items WHERE item_id=?");
    $stmt->execute([$_GET['delete']]);
    header("Location: menu.php");
    exit;
}

/* FETCH ITEMS */
$stmt = $pdo->query("
    SELECT m.*, c.category_name
    FROM menu_items m
    JOIN categories c ON m.category_id=c.category_id
    ORDER BY m.item_id DESC
");
$items = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Manage Menu</title>
<link rel="stylesheet" href="../assets/css/main.css">
<link rel="stylesheet" href="../assets/css/dashboard.css">
<link rel="stylesheet" href="../assets/css/staff_menu.css">
</head>
<body>

<?php include "../includes/navbar_staff.php"; ?>

<div class="dashboard-container">
<?php include "../includes/sidebar_staff.php"; ?>

<main class="main-content">

    <!-- PAGE HEADER -->
    <div class="pg-header">
        <div>
            <h1 class="pg-title">Menu Management</h1>
            <p class="pg-sub">Add items, update stock and remove listings</p>
        </div>
        <div class="pg-stats">
            <div class="pg-stat">
                <span class="pg-stat-num"><?= count($items) ?></span>
                <span class="pg-stat-label">Total</span>
            </div>
            <div class="pg-stat">
                <span class="pg-stat-num green"><?= count(array_filter($items, fn($i) => $i['availability'])) ?></span>
                <span class="pg-stat-label">Available</span>
            </div>
            <div class="pg-stat">
                <span class="pg-stat-num red"><?= count(array_filter($items, fn($i) => !$i['availability'])) ?></span>
                <span class="pg-stat-label">Out of Stock</span>
            </div>
        </div>
    </div>

    <!-- ADD ITEM FORM -->
    <div class="add-card">
        <div class="add-card-head">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            Add New Menu Item
        </div>

        <form method="POST" enctype="multipart/form-data" class="add-form">

            <div class="add-row">
                <div class="add-field">
                    <label>Item Name <span>*</span></label>
                    <input type="text" name="item_name" placeholder="e.g. Masala Dosa" required>
                </div>
                <div class="add-field">
                    <label>Price (₹) <span>*</span></label>
                    <input type="number" step="0.01" name="price" placeholder="e.g. 60.00" required>
                </div>
                <div class="add-field">
                    <label>Stock <span>*</span></label>
                    <input type="number" name="stock" placeholder="e.g. 50" min="0" required>
                </div>
            </div>

            <div class="add-row">
                <div class="add-field">
                    <label>Category <span>*</span></label>
                    <select name="category_id" required>
                        <option value="">Select category</option>
                        <?php foreach($categories as $cat): ?>
                        <option value="<?= $cat['category_id'] ?>"><?= htmlspecialchars($cat['category_name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="add-field add-field-wide">
                    <label>Description</label>
                    <input type="text" name="description" placeholder="Short description (optional)">
                </div>
            </div>

            <div class="add-row add-row-last">
                <div class="add-field">
                    <label>Item Photo</label>
                    <input type="file" name="image" accept="image/*">
                </div>
                <button type="submit" name="add_item" class="add-btn">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                    Add to Menu
                </button>
            </div>

        </form>
    </div>

    <!-- MENU GRID -->
    <?php if(empty($items)): ?>
    <div class="empty-state">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.4" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6z"/></svg>
        <strong>No items yet</strong>
        <p>Use the form above to add your first menu item</p>
    </div>
    <?php else: ?>

    <div class="items-grid">
    <?php foreach($items as $idx => $item): ?>

    <div class="item-card" style="animation-delay:<?= $idx * 0.04 ?>s">

        <!-- IMAGE -->
        <div class="item-img-box">
            <?php if(!empty($item['image'])): ?>
                <img src="../uploads/<?= htmlspecialchars($item['image']) ?>"
                     alt="<?= htmlspecialchars($item['item_name']) ?>"
                     onerror="this.parentElement.classList.add('no-img')">
            <?php else: ?>
                <div class="item-img-placeholder">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.4" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/></svg>
                </div>
            <?php endif; ?>
            <span class="item-badge <?= $item['availability'] ? 'avail' : 'oos' ?>">
                <?= $item['availability'] ? 'Available' : 'Out of Stock' ?>
            </span>
        </div>

        <!-- BODY -->
        <div class="item-body">

            <div class="item-head-row">
                <div>
                    <div class="item-name"><?= htmlspecialchars($item['item_name']) ?></div>
                    <span class="item-cat"><?= htmlspecialchars($item['category_name']) ?></span>
                </div>
                <div class="item-price">₹<?= number_format($item['price'], 2) ?></div>
            </div>

            <?php if(!empty($item['description'])): ?>
            <div class="item-desc"><?= htmlspecialchars($item['description']) ?></div>
            <?php endif; ?>

            <div class="item-stock-pill">
                Stock: <strong><?= $item['stock'] ?></strong>
            </div>

            <!-- UPDATE STOCK FORM -->
            <form method="POST" class="stock-update-form">
                <input type="hidden" name="item_id" value="<?= $item['item_id'] ?>">
                <input type="number" name="new_stock" placeholder="New stock qty" min="0" required>
                <button type="submit" name="update_stock" class="btn-update">Update</button>
            </form>

            <!-- DELETE -->
            <a href="?delete=<?= $item['item_id'] ?>"
               class="btn-delete"
               onclick="return confirm('Delete \'<?= addslashes($item['item_name']) ?>\'? This cannot be undone.')">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                Delete Item
            </a>

        </div>
    </div>

    <?php endforeach; ?>
    </div>

    <?php endif; ?>

</main>
</div>
</body>
</html>