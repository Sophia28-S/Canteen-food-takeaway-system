<?php
require "../api/session.php";
requireRole('admin');
require "../api/db.php";

$stmt = $pdo->query("SELECT * FROM system_settings WHERE id=1");
$settings = $stmt->fetch();

if(isset($_POST['save_settings'])){
    $stmt = $pdo->prepare("
        UPDATE system_settings SET
        system_name=?, contact_email=?, contact_phone=?,
        ordering_enabled=?, cash_enabled=?, online_enabled=?, scheduled_enabled=?,
        tax_percentage=?, service_charge=?, currency_symbol=?,
        auto_cancel_minutes=?, max_items_per_order=?,
        low_stock_alert=?, sound_alert=?
        WHERE id=1
    ");
    $stmt->execute([
        $_POST['system_name'], $_POST['contact_email'], $_POST['contact_phone'],
        isset($_POST['ordering_enabled'])?1:0, isset($_POST['cash_enabled'])?1:0,
        isset($_POST['online_enabled'])?1:0, isset($_POST['scheduled_enabled'])?1:0,
        $_POST['tax_percentage'], $_POST['service_charge'], $_POST['currency_symbol'],
        $_POST['auto_cancel_minutes'], $_POST['max_items_per_order'],
        isset($_POST['low_stock_alert'])?1:0, isset($_POST['sound_alert'])?1:0
    ]);
    header("Location: settings.php?success=1"); exit;
}
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>System Settings</title>
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
            <h1 class="admin-page-title">System Settings</h1>
            <p class="admin-page-sub">Configure canteen system behaviour and rules</p>
        </div>
    </div>

    <?php if(isset($_GET['success'])): ?>
    <div class="admin-toast success">✅ Settings saved successfully. Changes are now live.</div>
    <?php endif; ?>

    <form method="POST" class="admin-form">

        <!-- GENERAL -->
        <div class="admin-panel">
            <div class="admin-panel-head">
                <h3>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.343 3.94c.09-.542.56-.94 1.11-.94h1.093c.55 0 1.02.398 1.11.94l.149.894c.07.424.384.764.78.93.398.164.855.142 1.205-.108l.737-.527a1.125 1.125 0 011.45.12l.773.774c.39.389.44 1.002.12 1.45l-.527.737c-.25.35-.272.806-.107 1.204.165.397.505.71.93.78l.893.15c.543.09.94.56.94 1.109v1.094c0 .55-.397 1.02-.94 1.11l-.893.149c-.425.07-.765.383-.93.78-.165.398-.143.854.107 1.204l.527.738c.32.447.269 1.06-.12 1.45l-.774.773a1.125 1.125 0 01-1.449.12l-.738-.527c-.35-.25-.806-.272-1.203-.107-.397.165-.71.505-.781.929l-.149.894c-.09.542-.56.94-1.11.94h-1.094c-.55 0-1.019-.398-1.11-.94l-.148-.894c-.071-.424-.384-.764-.781-.93-.398-.164-.854-.142-1.204.108l-.738.527c-.447.32-1.06.269-1.45-.12l-.773-.774a1.125 1.125 0 01-.12-1.45l.527-.737c.25-.35.273-.806.108-1.204-.165-.397-.505-.71-.93-.78l-.894-.15c-.542-.09-.94-.56-.94-1.109v-1.094c0-.55.398-1.02.94-1.11l.894-.149c.424-.07.765-.383.93-.78.165-.398.143-.854-.107-1.204l-.527-.738a1.125 1.125 0 01.12-1.45l.773-.773a1.125 1.125 0 011.45-.12l.737.527c.35.25.807.272 1.204.107.397-.165.71-.505.78-.929l.15-.894z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    General
                </h3>
            </div>
            <div class="admin-panel-body">
                <div class="admin-form-row">
                    <div class="admin-field">
                        <label>System Name</label>
                        <input type="text" name="system_name" value="<?= htmlspecialchars($settings['system_name']) ?>">
                    </div>
                    <div class="admin-field">
                        <label>Contact Email</label>
                        <input type="email" name="contact_email" value="<?= htmlspecialchars($settings['contact_email']) ?>">
                    </div>
                    <div class="admin-field">
                        <label>Contact Phone</label>
                        <input type="text" name="contact_phone" value="<?= htmlspecialchars($settings['contact_phone']) ?>">
                    </div>
                </div>
            </div>
        </div>

        <!-- ORDERING CONTROLS -->
        <div class="admin-panel">
            <div class="admin-panel-head">
                <h3>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119 1.007z"/></svg>
                    Ordering Controls
                </h3>
            </div>
            <div class="admin-panel-body">
                <div class="admin-check-group">
                    <label class="admin-check">
                        <input type="checkbox" name="ordering_enabled" <?= $settings['ordering_enabled']?'checked':'' ?>>
                        Enable Ordering — students can place orders
                    </label>
                    <label class="admin-check">
                        <input type="checkbox" name="cash_enabled" <?= $settings['cash_enabled']?'checked':'' ?>>
                        Enable Cash Payment
                    </label>
                    <label class="admin-check">
                        <input type="checkbox" name="online_enabled" <?= $settings['online_enabled']?'checked':'' ?>>
                        Enable Online / UPI Payment
                    </label>
                    <label class="admin-check">
                        <input type="checkbox" name="scheduled_enabled" <?= $settings['scheduled_enabled']?'checked':'' ?>>
                        Enable Scheduled Pickup Orders
                    </label>
                </div>
            </div>
        </div>

        <!-- FINANCIAL -->
        <div class="admin-panel">
            <div class="admin-panel-head">
                <h3>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z"/></svg>
                    Financial
                </h3>
            </div>
            <div class="admin-panel-body">
                <div class="admin-form-row">
                    <div class="admin-field">
                        <label>Tax Percentage (%)</label>
                        <input type="number" step="0.01" name="tax_percentage" value="<?= $settings['tax_percentage'] ?>">
                    </div>
                    <div class="admin-field">
                        <label>Service Charge (%)</label>
                        <input type="number" step="0.01" name="service_charge" value="<?= $settings['service_charge'] ?>">
                    </div>
                    <div class="admin-field">
                        <label>Currency Symbol</label>
                        <input type="text" name="currency_symbol" value="<?= htmlspecialchars($settings['currency_symbol']) ?>">
                    </div>
                </div>
            </div>
        </div>

        <!-- OPERATIONAL -->
        <div class="admin-panel">
            <div class="admin-panel-head">
                <h3>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Operational
                </h3>
            </div>
            <div class="admin-panel-body">
                <div class="admin-form-row">
                    <div class="admin-field">
                        <label>Auto-Cancel Pending Orders (minutes)</label>
                        <input type="number" name="auto_cancel_minutes" value="<?= $settings['auto_cancel_minutes'] ?>">
                    </div>
                    <div class="admin-field">
                        <label>Max Items Per Order</label>
                        <input type="number" name="max_items_per_order" value="<?= $settings['max_items_per_order'] ?>">
                    </div>
                </div>
            </div>
        </div>

        <!-- NOTIFICATIONS -->
        <div class="admin-panel">
            <div class="admin-panel-head">
                <h3>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/></svg>
                    Notifications
                </h3>
            </div>
            <div class="admin-panel-body">
                <div class="admin-check-group">
                    <label class="admin-check">
                        <input type="checkbox" name="low_stock_alert" <?= $settings['low_stock_alert']?'checked':'' ?>>
                        Enable Low Stock Alerts for staff
                    </label>
                    <label class="admin-check">
                        <input type="checkbox" name="sound_alert" <?= $settings['sound_alert']?'checked':'' ?>>
                        Enable Sound Alert for staff on new orders
                    </label>
                </div>
            </div>
        </div>

        <div>
            <button type="submit" name="save_settings" class="btn-primary" style="padding:13px 32px;font-size:15px;">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                Save All Settings
            </button>
        </div>

    </form>

</main>
</div>
</body>
</html>