<?php
require "../api/session.php";
requireRole('admin');
require "../api/db.php";

if (isset($_POST['create_user'])) {
    $name=$_POST['name']; $email=$_POST['email']; $password=$_POST['password']; $role=$_POST['role'];
    if($name&&$email&&$password&&$role){
        $hashed=password_hash($password,PASSWORD_DEFAULT);
        $pdo->prepare("INSERT INTO users (name,email,password,role) VALUES (?,?,?,?)")->execute([$name,$email,$hashed,$role]);
        header("Location: manage_users.php?success=created"); exit;
    }
}

if (isset($_POST['update_role'])) {
    $pdo->prepare("UPDATE users SET role=? WHERE user_id=?")->execute([$_POST['new_role'],$_POST['user_id']]);
    header("Location: manage_users.php?success=updated"); exit;
}

if (isset($_GET['delete'])) {
    if($_GET['delete'] != $_SESSION['user_id']){
        $pdo->prepare("DELETE FROM users WHERE user_id=?")->execute([$_GET['delete']]);
    }
    header("Location: manage_users.php?success=deleted"); exit;
}

$users = $pdo->query("SELECT * FROM users ORDER BY created_at DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Manage Users</title>
<link rel="stylesheet" href="../assets/css/main.css">
<link rel="stylesheet" href="../assets/css/dashboard.css">
<link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body>
<?php include "../includes/navbar_admin.php"; ?>
<div class="dashboard-container">
<?php include "../includes/sidebar_admin.php"; ?>
<main class="main-content">

    <div class="admin-page-header">
        <div>
            <h1 class="admin-page-title">User Management</h1>
            <p class="admin-page-sub">Create accounts and manage user roles</p>
        </div>
        <div style="display:flex;gap:8px;align-items:center;">
            <span style="font-size:13px;color:var(--text-muted);"><?= count($users) ?> users total</span>
        </div>
    </div>

    <?php if(isset($_GET['success'])): ?>
    <div class="admin-toast success">
        <?= $_GET['success']==='created' ? '✅ User created successfully.' : ($_GET['success']==='updated' ? '✅ Role updated.' : '🗑️ User deleted.') ?>
    </div>
    <?php endif; ?>

    <!-- CREATE USER FORM -->
    <div class="admin-panel">
        <div class="admin-panel-head dark">
            <h3>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Create New User
            </h3>
        </div>
        <div class="admin-panel-body">
            <form method="POST" class="admin-form">
                <div class="admin-form-row">
                    <div class="admin-field">
                        <label>Full Name <span>*</span></label>
                        <input type="text" name="name" placeholder="e.g. Ravi Kumar" required>
                    </div>
                    <div class="admin-field">
                        <label>Email <span>*</span></label>
                        <input type="email" name="email" placeholder="e.g. ravi@college.edu" required>
                    </div>
                    <div class="admin-field">
                        <label>Password <span>*</span></label>
                        <input type="password" name="password" placeholder="Min 8 characters" required>
                    </div>
                    <div class="admin-field">
                        <label>Role <span>*</span></label>
                        <select name="role" required>
                            <option value="student">Student</option>
                            <option value="staff">Staff</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                </div>
                <div>
                    <button type="submit" name="create_user" class="btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                        Create User
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- USERS LIST -->
    <div class="admin-panel">
        <div class="admin-panel-head">
            <h3>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                All Users
            </h3>
        </div>
        <div class="admin-panel-body" style="padding:0;">
            <?php if(empty($users)): ?>
            <div class="admin-empty"><span>No users found</span></div>
            <?php else: ?>
            <div class="data-list">
            <?php foreach($users as $user): ?>
            <div class="data-row">
                <div class="data-row-left">
                    <div class="data-row-title"><?= htmlspecialchars($user['name']) ?></div>
                    <div class="data-row-sub"><?= htmlspecialchars($user['email']) ?> · Joined <?= date('d M Y', strtotime($user['created_at'])) ?></div>
                </div>
                <div class="data-row-right">
                    <span class="role-pill <?= $user['role'] ?>"><?= ucfirst($user['role']) ?></span>

                    <?php if($user['user_id'] != $_SESSION['user_id']): ?>
                    <form method="POST" style="display:inline-flex;gap:6px;align-items:center;">
                        <input type="hidden" name="user_id" value="<?= $user['user_id'] ?>">
                        <select name="new_role" style="font-size:12px;padding:5px 8px;border:1.5px solid var(--border);border-radius:6px;background:var(--bg);outline:none;cursor:pointer;">
                            <option value="student" <?= $user['role']==='student'?'selected':'' ?>>Student</option>
                            <option value="staff"   <?= $user['role']==='staff'  ?'selected':'' ?>>Staff</option>
                            <option value="admin"   <?= $user['role']==='admin'  ?'selected':'' ?>>Admin</option>
                        </select>
                        <button type="submit" name="update_role" class="btn-sm btn-blue">Update</button>
                    </form>
                    <a href="?delete=<?= $user['user_id'] ?>"
                       class="btn-sm btn-danger"
                       onclick="return confirm('Delete <?= addslashes($user['name']) ?>?')">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/></svg>
                        Delete
                    </a>
                    <?php else: ?>
                    <span style="font-size:12px;color:var(--text-light);">You</span>
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