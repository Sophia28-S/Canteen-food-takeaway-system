<?php
require "../api/session.php";
requireRole('admin');
require "../api/db.php";

if(isset($_POST['add'])){
    $name=trim($_POST['category_name']);
    if($name){ $pdo->prepare("INSERT INTO categories (category_name) VALUES (?)")->execute([$name]); }
    header("Location: manage_categories.php?success=added"); exit;
}

if(isset($_GET['delete'])){
    $check=$pdo->prepare("SELECT COUNT(*) FROM menu_items WHERE category_id=?");
    $check->execute([$_GET['delete']]);
    if($check->fetchColumn()==0){
        $pdo->prepare("DELETE FROM categories WHERE category_id=?")->execute([$_GET['delete']]);
        header("Location: manage_categories.php?success=deleted");
    } else {
        header("Location: manage_categories.php?error=has_items");
    }
    exit;
}

$categories = $pdo->query("
    SELECT c.*, COUNT(m.item_id) as item_count 
    FROM categories c 
    LEFT JOIN menu_items m ON c.category_id=m.category_id 
    GROUP BY c.category_id 
    ORDER BY c.category_id DESC
")->fetchAll();
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Manage Categories</title>
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
            <h1 class="admin-page-title">Menu Categories</h1>
            <p class="admin-page-sub">Organise menu items into categories</p>
        </div>
    </div>

    <?php if(isset($_GET['success'])): ?>
    <div class="admin-toast success"><?= $_GET['success']==='added'?'✅ Category added.':'🗑️ Category deleted.' ?></div>
    <?php endif; ?>
    <?php if(isset($_GET['error'])): ?>
    <div class="admin-toast error">❌ Cannot delete — this category has menu items. Remove items first.</div>
    <?php endif; ?>

    <div class="admin-panel">
        <div class="admin-panel-head dark">
            <h3>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Add Category
            </h3>
        </div>
        <div class="admin-panel-body">
            <form method="POST" class="admin-form">
                <div class="admin-form-row">
                    <div class="admin-field">
                        <label>Category Name <span>*</span></label>
                        <input type="text" name="category_name" placeholder="e.g. Beverages, Snacks, Meals" required>
                    </div>
                    <div style="display:flex;align-items:flex-end;">
                        <button type="submit" name="add" class="btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                            Add Category
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="admin-panel">
        <div class="admin-panel-head">
            <h3>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z"/><path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z"/></svg>
                All Categories
            </h3>
            <span style="font-size:13px;color:var(--text-muted);"><?= count($categories) ?> categories</span>
        </div>
        <div class="admin-panel-body" style="padding:0;">
            <?php if(empty($categories)): ?>
            <div class="admin-empty"><span>No categories yet. Add one above.</span></div>
            <?php else: ?>
            <div class="data-list">
            <?php foreach($categories as $cat): ?>
            <div class="data-row">
                <div class="data-row-left">
                    <div class="data-row-title"><?= htmlspecialchars($cat['category_name']) ?></div>
                    <div class="data-row-sub"><?= $cat['item_count'] ?> menu item<?= $cat['item_count']!=1?'s':'' ?></div>
                </div>
                <div class="data-row-right">
                    <?php if($cat['item_count'] == 0): ?>
                    <a href="?delete=<?= $cat['category_id'] ?>"
                       class="btn-sm btn-danger"
                       onclick="return confirm('Delete \'<?= addslashes($cat['category_name']) ?>\'?')">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                        Delete
                    </a>
                    <?php else: ?>
                    <span style="font-size:12px;color:var(--text-light);">Has items — cannot delete</span>
                    <?php endif; ?>
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