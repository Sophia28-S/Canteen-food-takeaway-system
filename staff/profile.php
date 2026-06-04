<?php
require "../api/session.php";
requireRole('staff');
require "../api/db.php";

$userId = $_SESSION['user_id'];

/* FETCH USER */
$stmt = $pdo->prepare("SELECT * FROM users WHERE user_id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch();

/* UPDATE PROFILE */
if (isset($_POST['update_profile'])) {

    $name  = trim($_POST['name']);
    $email = trim($_POST['email']);

    $stmt = $pdo->prepare("
        UPDATE users SET name = ?, email = ?
        WHERE user_id = ?
    ");
    $stmt->execute([$name, $email, $userId]);

    $_SESSION['name'] = $name;

    header("Location: profile.php");
    exit;
}

/* UPDATE PASSWORD */
if (isset($_POST['update_password'])) {

    $newPassword = password_hash($_POST['new_password'], PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("
        UPDATE users SET password = ?
        WHERE user_id = ?
    ");
    $stmt->execute([$newPassword, $userId]);

    header("Location: profile.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Staff Profile</title>
<link rel="stylesheet" href="../assets/css/main.css">
<link rel="stylesheet" href="../assets/css/dashboard.css">
<link rel="stylesheet" href="../assets/css/profile_staff.css">
</head>

<body class="staff-theme">

<?php include "../includes/navbar_staff.php"; ?>

<div class="dashboard-container">

<?php include "../includes/sidebar_staff.php"; ?>

<main class="main-content">

    <div class="profile-container">
        <div class="profile-card">

            <!-- HEADER -->
            <div class="profile-header">
                <div class="profile-avatar" data-initial="<?= strtoupper(substr($user['name'], 0, 1)) ?>"></div>
                <div class="profile-info">
                    <h2><?= htmlspecialchars($user['name']) ?></h2>
                    <p class="profile-email"><?= htmlspecialchars($user['email']) ?></p>
                </div>
                <span class="role-badge"><?= ucfirst($user['role']) ?></span>
            </div>

            <!-- PERSONAL INFO -->
            <div class="profile-section">
                <h3>Personal Information</h3>
                <form method="POST" class="profile-form">
                    <div class="form-group">
                        <label>Full Name</label>
                        <input type="text" name="name"
                            value="<?= htmlspecialchars($user['name']) ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Email Address</label>
                        <input type="email" name="email"
                            value="<?= htmlspecialchars($user['email']) ?>" required>
                    </div>
                    <button type="submit" name="update_profile" class="primary-btn">
                        Save Changes
                    </button>
                </form>
            </div>

            <div class="profile-divider"></div>

            <!-- SECURITY -->
            <div class="profile-section">
                <h3>Security</h3>
                <form method="POST" class="profile-form">
                    <div class="form-group">
                        <label>New Password</label>
                        <input type="password" name="new_password" required>
                    </div>
                    <button type="submit" name="update_password" class="primary-btn">
                        Update Password
                    </button>
                </form>
            </div>

            <!-- META -->
            <div class="account-meta">
                Account created on: <?= date("F d, Y", strtotime($user['created_at'])) ?>
            </div>

        </div>
    </div>

</main>
</div>

</body>
</html>