<?php
require "../api/db.php";
require "../api/session.php";

$error = "";
$emailVal = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $email    = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $emailVal = htmlspecialchars($email);

    if (empty($email) || empty($password)) {
        $error = "Please fill in all fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } else {
        $stmt = $pdo->prepare("SELECT user_id, name, password, role FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || !password_verify($password, $user['password'])) {
            $error = "Incorrect email or password.";
        } else {
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['name']    = $user['name'];
            $_SESSION['role']    = $user['role'];

            switch ($user['role']) {
                case 'admin': header("Location: ../admin/dashboard.php"); break;
                case 'staff': header("Location: ../staff/dashboard.php"); break;
                default:      header("Location: ../student/dashboard.php");
            }
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Canteen Takeaway</title>
    <link rel="stylesheet" href="../assets/css/auth.css">
</head>
<body>

<div class="auth-split">

    <!-- LEFT PANEL -->
    <div class="auth-brand">
        <div class="brand-inner">
            <div class="brand-logo">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8.25v-1.5m0 1.5c-1.355 0-2.697.056-4.024.166C6.845 8.51 6 9.473 6 10.608v2.513m6-4.871c1.355 0 2.697.056 4.024.166C17.155 8.51 18 9.473 18 10.608v2.513M15 21h3.75M15 21H9m6 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.375a1.125 1.125 0 00-1.125 1.125V21M9 21H5.25M9 21v-3.375c0-.621.504-1.125 1.125-1.125h.375c.621 0 1.125.504 1.125 1.125V21m0 0h6"/></svg>
            </div>
            <h1 class="brand-name">Canteen<br>Takeaway</h1>
            <p class="brand-tagline">Skip the queue.<br>Order ahead. Eat smart.</p>
            <div class="brand-dots">
                <span></span><span></span><span></span>
            </div>
        </div>
    </div>

    <!-- RIGHT PANEL -->
    <div class="auth-panel">
        <div class="auth-card">

            <div class="auth-card-head">
                <h2>Welcome back</h2>
                <p>Sign in to your account to continue</p>
            </div>

            <?php if(isset($_GET["registered"])): ?>
            <div class="auth-success">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                Account created! You can now sign in.
            </div>
            <?php endif; ?>
            <?php if ($error): ?>
            <div class="auth-error" id="authError">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                <?= htmlspecialchars($error) ?>
            </div>
            <?php endif; ?>

            <form method="POST" id="loginForm" novalidate>

                <div class="auth-field" id="fieldEmail">
                    <label for="email">Email address</label>
                    <div class="input-wrap">
                        <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
                        <input type="email" name="email" id="email" placeholder="you@college.edu" value="<?= $emailVal ?>" autocomplete="email">
                    </div>
                    <span class="field-error" id="emailError"></span>
                </div>

                <div class="auth-field" id="fieldPassword">
                    <label for="password">Password</label>
                    <div class="input-wrap">
                        <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
                        <input type="password" name="password" id="password" placeholder="Your password" autocomplete="current-password">
                        <button type="button" class="toggle-pw" id="togglePw" tabindex="-1">
                            <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </button>
                    </div>
                    <span class="field-error" id="passwordError"></span>
                </div>

                <button type="submit" class="auth-btn" id="loginBtn">
                    <span class="btn-text">Sign In</span>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12h15m0 0l-6.75-6.75M19.5 12l-6.75 6.75"/></svg>
                </button>

            </form>

            <p class="auth-switch">
                New here? <a href="register.php">Create an account</a>
            </p>

        </div>
    </div>

</div>

<script>
const form = document.getElementById('loginForm');

function setError(fieldId, errorId, msg) {
    document.getElementById(fieldId).classList.add('has-error');
    document.getElementById(fieldId).classList.remove('has-success');
    document.getElementById(errorId).textContent = msg;
}

function setSuccess(fieldId) {
    document.getElementById(fieldId).classList.remove('has-error');
    document.getElementById(fieldId).classList.add('has-success');
}

function clearState(fieldId, errorId) {
    document.getElementById(fieldId).classList.remove('has-error','has-success');
    document.getElementById(errorId).textContent = '';
}

// Live validation
document.getElementById('email').addEventListener('blur', function() {
    const v = this.value.trim();
    if (!v) setError('fieldEmail','emailError','Email is required.');
    else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v)) setError('fieldEmail','emailError','Enter a valid email address.');
    else setSuccess('fieldEmail');
});

document.getElementById('password').addEventListener('blur', function() {
    if (!this.value) setError('fieldPassword','passwordError','Password is required.');
    else setSuccess('fieldPassword');
});

document.getElementById('email').addEventListener('input', function() {
    if (document.getElementById('fieldEmail').classList.contains('has-error')) clearState('fieldEmail','emailError');
});

document.getElementById('password').addEventListener('input', function() {
    if (document.getElementById('fieldPassword').classList.contains('has-error')) clearState('fieldPassword','passwordError');
});

// Submit validation
form.addEventListener('submit', function(e) {
    let valid = true;
    const email = document.getElementById('email').value.trim();
    const pass  = document.getElementById('password').value;

    if (!email) { setError('fieldEmail','emailError','Email is required.'); valid = false; }
    else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) { setError('fieldEmail','emailError','Enter a valid email address.'); valid = false; }
    else setSuccess('fieldEmail');

    if (!pass) { setError('fieldPassword','passwordError','Password is required.'); valid = false; }
    else setSuccess('fieldPassword');

    if (!valid) e.preventDefault();
    else {
        const btn = document.getElementById('loginBtn');
        btn.classList.add('loading');
        btn.querySelector('.btn-text').textContent = 'Signing in…';
    }
});

// Toggle password
document.getElementById('togglePw').addEventListener('click', function() {
    const pw = document.getElementById('password');
    pw.type = pw.type === 'password' ? 'text' : 'password';
});
</script>

</body>
</html>