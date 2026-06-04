<?php
session_start();

// If user already logged in → redirect to correct dashboard
if (isset($_SESSION['user_id']) && isset($_SESSION['role'])) {

    switch ($_SESSION['role']) {

        case 'admin':
            header("Location: admin/dashboard.php");
            break;

        case 'staff':
            header("Location: staff/dashboard.php");
            break;

        case 'student':
            header("Location: student/dashboard.php");
            break;

        default:
            // If role somehow invalid → destroy session
            session_destroy();
            header("Location: auth/login.php");
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Canteen Takeaway System</title>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/index.css">
</head>
<body>

<div class="auth-box">
    <h2>🍔 College Canteen Takeaway</h2>
    <p class="subtitle">Order ahead. Skip the queue.</p>

    <a href="auth/login.php">
        <button>Login</button>
    </a>

    <p class="switch">
        Don’t have an account?
        <a href="auth/register.php">Create new account</a>
    </p>
</div>

</body>
</html>
