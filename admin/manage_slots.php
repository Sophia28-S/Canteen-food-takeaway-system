<?php
require "../api/session.php";
requireRole('admin');
require "../api/db.php";

if(isset($_POST['add'])){
    $time=trim($_POST['slot_time']);
    if($time){ $pdo->prepare("INSERT INTO pickup_slots (slot_time) VALUES (?)")->execute([$time]); }
    header("Location: manage_slots.php?success=added"); exit;
}

if(isset($_GET['toggle'])){
    $pdo->prepare("UPDATE pickup_slots SET is_active=!is_active WHERE slot_id=?")->execute([$_GET['toggle']]);
    header("Location: manage_slots.php?success=toggled"); exit;
}

if(isset($_GET['delete'])){
    $pdo->prepare("DELETE FROM pickup_slots WHERE slot_id=?")->execute([$_GET['delete']]);
    header("Location: manage_slots.php?success=deleted"); exit;
}

$slots = $pdo->query("SELECT * FROM pickup_slots ORDER BY slot_id ASC")->fetchAll();
$activeCount = count(array_filter($slots, fn($s) => $s['is_active']));
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Manage Pickup Slots</title>
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
            <h1 class="admin-page-title">Pickup Slots</h1>
            <p class="admin-page-sub">Manage student pickup time windows</p>
        </div>
        <div style="display:flex;gap:8px;">
            <span class="active-pill"><?= $activeCount ?> active</span>
            <span class="inactive-pill"><?= count($slots)-$activeCount ?> inactive</span>
        </div>
    </div>

    <?php if(isset($_GET['success'])): ?>
    <div class="admin-toast success">✅ Slot <?= $_GET['success']==='added'?'added':($_GET['success']==='deleted'?'deleted':'updated') ?> successfully.</div>
    <?php endif; ?>

    <div class="admin-panel">
        <div class="admin-panel-head dark">
            <h3>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Add Pickup Slot
            </h3>
        </div>
        <div class="admin-panel-body">
            <form method="POST" class="admin-form">
                <div class="admin-form-row">
                    <div class="admin-field">
                        <label>Slot Time <span>*</span></label>
                        <input type="text" name="slot_time" placeholder="e.g. 12:00 PM - 12:30 PM" required>
                    </div>
                    <div style="display:flex;align-items:flex-end;">
                        <button type="submit" name="add" class="btn-primary">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                            Add Slot
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="admin-panel">
        <div class="admin-panel-head">
            <h3>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                All Slots
            </h3>
        </div>
        <div class="admin-panel-body" style="padding:0;">
            <?php if(empty($slots)): ?>
            <div class="admin-empty"><span>No slots yet. Add one above.</span></div>
            <?php else: ?>
            <div class="data-list">
            <?php foreach($slots as $slot): ?>
            <div class="data-row">
                <div class="data-row-left">
                    <div class="data-row-title">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" style="width:15px;height:15px;display:inline;margin-right:5px;color:var(--primary);vertical-align:middle;"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <?= htmlspecialchars($slot['slot_time']) ?>
                    </div>
                </div>
                <div class="data-row-right">
                    <span class="<?= $slot['is_active']?'active-pill':'inactive-pill' ?>">
                        <?= $slot['is_active']?'Active':'Inactive' ?>
                    </span>
                    <a href="?toggle=<?= $slot['slot_id'] ?>" class="btn-sm <?= $slot['is_active']?'btn-ghost':'btn-success' ?>">
                        <?= $slot['is_active']?'Disable':'Enable' ?>
                    </a>
                    <a href="?delete=<?= $slot['slot_id'] ?>"
                       class="btn-sm btn-danger"
                       onclick="return confirm('Delete this slot?')">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                    </a>
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