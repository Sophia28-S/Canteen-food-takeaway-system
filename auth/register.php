<?php
require "../api/db.php";
require "../api/session.php";

$error   = "";
$success = "";
$nameVal = $emailVal = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $name     = trim($_POST['name']     ?? '');
    $email    = trim($_POST['email']    ?? '');
    $password = $_POST['password']      ?? '';
    $confirm  = $_POST['confirm']       ?? '';

    $nameVal  = htmlspecialchars($name);
    $emailVal = htmlspecialchars($email);

    if (empty($name) || empty($email) || empty($password) || empty($confirm)) {
        $error = "All fields are required.";
    } elseif (strlen($name) < 2) {
        $error = "Name must be at least 2 characters.";
    } elseif (!preg_match("/^[a-zA-Z\s]+$/", $name)) {
        $error = "Name can only contain letters and spaces.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Please enter a valid email address.";
    } elseif (!preg_match("/@kristujayanti\.com$/i", $email)) {
        $error = "Only @kristujayanti.com emails are allowed.";
    } elseif (strlen($password) < 6) {
        $error = "Password must be at least 6 characters.";
    } elseif ($password !== $confirm) {
        $error = "Passwords do not match.";
    } else {
        $stmt = $pdo->prepare("SELECT user_id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = "This email is already registered.";
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'student')")
                ->execute([$name, $email, $hashed]);
            header("Location: login.php?registered=1");
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
    <title>Register — Canteen Takeaway</title>
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
            <p class="brand-tagline">Join thousands of students<br>ordering smarter every day.</p>
            <div class="brand-dots">
                <span></span><span></span><span></span>
            </div>
        </div>
    </div>

    <!-- RIGHT PANEL -->
    <div class="auth-panel">
        <div class="auth-card">

            <div class="auth-card-head">
                <h2>Create account</h2>
                <p>Start ordering from your canteen today</p>
            </div>

            <?php if ($error): ?>
            <div class="auth-error">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z"/></svg>
                <?= htmlspecialchars($error) ?>
            </div>
            <?php endif; ?>

            <form method="POST" id="registerForm" novalidate>

                <div class="auth-field" id="fieldName">
                    <label for="name">Full Name</label>
                    <div class="input-wrap">
                        <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                        <input type="text" name="name" id="name" placeholder="Ravi Kumar" value="<?= $nameVal ?>" autocomplete="name">
                    </div>
                    <span class="field-error" id="nameError"></span>
                </div>

                <div class="auth-field" id="fieldEmail">
                    <label for="email">Email address</label>
                    <div class="input-wrap">
                        <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75"/></svg>
                        <input type="email" name="email" id="email" placeholder="you@kristujayanti.com" value="<?= $emailVal ?>" autocomplete="email">
                    </div>
                    <span class="field-error" id="emailError"></span>
                </div>

                <div class="auth-field" id="fieldPassword">
                    <label for="password">Password</label>
                    <div class="input-wrap">
                        <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/></svg>
                        <input type="password" name="password" id="password" placeholder="Min 6 characters" autocomplete="new-password">
                        <button type="button" class="toggle-pw" id="togglePw" tabindex="-1">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                        </button>
                    </div>
                    <div class="pw-strength" id="pwStrength">
                        <div class="pw-bar"><div class="pw-fill" id="pwFill"></div></div>
                        <span id="pwLabel"></span>
                    </div>
                    <span class="field-error" id="passwordError"></span>
                </div>

                <div class="auth-field" id="fieldConfirm">
                    <label for="confirm">Confirm Password</label>
                    <div class="input-wrap">
                        <svg class="input-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
                        <input type="password" name="confirm" id="confirm" placeholder="Repeat your password" autocomplete="new-password">
                    </div>
                    <span class="field-error" id="confirmError"></span>
                </div>

                <button type="submit" class="auth-btn" id="registerBtn">
                    <span class="btn-text">Create Account</span>
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12h15m0 0l-6.75-6.75M19.5 12l-6.75 6.75"/></svg>
                </button>

            </form>

            <p class="auth-switch">
                Already have an account? <a href="login.php">Sign in</a>
            </p>

        </div>
    </div>

</div>

<script>
const form = document.getElementById('registerForm');

function setError(fieldId, errorId, msg) {
    document.getElementById(fieldId).classList.add('has-error');
    document.getElementById(fieldId).classList.remove('has-success');
    if (errorId) document.getElementById(errorId).textContent = msg;
}

function setSuccess(fieldId) {
    document.getElementById(fieldId).classList.remove('has-error');
    document.getElementById(fieldId).classList.add('has-success');
}

function clearField(fieldId, errorId) {
    document.getElementById(fieldId).classList.remove('has-error','has-success');
    if (errorId) document.getElementById(errorId).textContent = '';
}

// Password strength
document.getElementById('password').addEventListener('input', function() {
    const v = this.value;
    const fill = document.getElementById('pwFill');
    const label = document.getElementById('pwLabel');
    document.getElementById('pwStrength').style.display = v ? 'flex' : 'none';

    let score = 0;
    if (v.length >= 6)  score++;
    if (v.length >= 10) score++;
    if (/[A-Z]/.test(v)) score++;
    if (/[0-9]/.test(v)) score++;
    if (/[^A-Za-z0-9]/.test(v)) score++;

    const levels = [
        { pct:'20%', color:'#ef4444', text:'Very weak' },
        { pct:'40%', color:'#f97316', text:'Weak' },
        { pct:'60%', color:'#f59e0b', text:'Fair' },
        { pct:'80%', color:'#22c55e', text:'Strong' },
        { pct:'100%', color:'#16a34a', text:'Very strong' }
    ];
    const l = levels[Math.max(0, score-1)];
    fill.style.width = l.pct;
    fill.style.background = l.color;
    label.textContent = l.text;
    label.style.color = l.color;

    if (document.getElementById('fieldPassword').classList.contains('has-error') && v.length >= 6) {
        clearField('fieldPassword', 'passwordError');
    }
});

// Live blur validation
document.getElementById('name').addEventListener('blur', function() {
    const v = this.value.trim();
    if (!v) setError('fieldName','nameError','Full name is required.');
    else if (v.length < 2) setError('fieldName','nameError','Name must be at least 2 characters.');
    else if (!/^[a-zA-Z\s]+$/.test(v)) setError('fieldName','nameError','Name can only contain letters and spaces.');
    else setSuccess('fieldName');
});

document.getElementById('email').addEventListener('blur', function() {
    const v = this.value.trim();
    if (!v) setError('fieldEmail','emailError','Email is required.');
    else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(v)) setError('fieldEmail','emailError','Enter a valid email address.');
    else if (!v.toLowerCase().endsWith('@kristujayanti.com')) setError('fieldEmail','emailError','Only @kristujayanti.com emails are allowed.');
    else setSuccess('fieldEmail');
});

document.getElementById('password').addEventListener('blur', function() {
    if (!this.value) setError('fieldPassword','passwordError','Password is required.');
    else if (this.value.length < 6) setError('fieldPassword','passwordError','Password must be at least 6 characters.');
    else setSuccess('fieldPassword');
});

document.getElementById('confirm').addEventListener('blur', function() {
    const pw = document.getElementById('password').value;
    if (!this.value) setError('fieldConfirm','confirmError','Please confirm your password.');
    else if (this.value !== pw) setError('fieldConfirm','confirmError','Passwords do not match.');
    else setSuccess('fieldConfirm');
});

// Clear on input
['name','email','password','confirm'].forEach(id => {
    document.getElementById(id).addEventListener('input', function() {
        const fieldMap = {name:'fieldName',email:'fieldEmail',password:'fieldPassword',confirm:'fieldConfirm'};
        const errMap   = {name:'nameError',email:'emailError',password:'passwordError',confirm:'confirmError'};
        if (document.getElementById(fieldMap[id]).classList.contains('has-error')) {
            clearField(fieldMap[id], errMap[id]);
        }
    });
});

// Submit
form.addEventListener('submit', function(e) {
    let valid = true;
    const name  = document.getElementById('name').value.trim();
    const email = document.getElementById('email').value.trim();
    const pw    = document.getElementById('password').value;
    const conf  = document.getElementById('confirm').value;

    // Validate Name
    if (!name || name.length < 2) { 
        setError('fieldName','nameError', !name ? 'Full name is required.' : 'Name must be at least 2 characters.'); 
        valid=false; 
    }
    else if (!/^[a-zA-Z\s]+$/.test(name)) {
        setError('fieldName','nameError','Name can only contain letters and spaces.');
        valid=false;
    }
    else setSuccess('fieldName');

    // Validate Email
    if (!email) { 
        setError('fieldEmail','emailError','Email is required.'); 
        valid=false; 
    }
    else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) { 
        setError('fieldEmail','emailError','Enter a valid email address.'); 
        valid=false; 
    }
    else if (!email.toLowerCase().endsWith('@kristujayanti.com')) {
        setError('fieldEmail','emailError','Only @kristujayanti.com emails are allowed.');
        valid=false;
    }
    else setSuccess('fieldEmail');

    // Validate Password
    if (!pw) { 
        setError('fieldPassword','passwordError','Password is required.'); 
        valid=false; 
    }
    else if (pw.length < 6) { 
        setError('fieldPassword','passwordError','Password must be at least 6 characters.'); 
        valid=false; 
    }
    else setSuccess('fieldPassword');

    // Validate Confirm Password
    if (!conf) { 
        setError('fieldConfirm','confirmError','Please confirm your password.'); 
        valid=false; 
    }
    else if (conf !== pw) { 
        setError('fieldConfirm','confirmError','Passwords do not match.'); 
        valid=false; 
    }
    else setSuccess('fieldConfirm');

    if (!valid) e.preventDefault();
    else {
        const btn = document.getElementById('registerBtn');
        btn.classList.add('loading');
        btn.querySelector('.btn-text').textContent = 'Creating account…';
    }
});

// Toggle pw
document.getElementById('togglePw').addEventListener('click', function() {
    const pw = document.getElementById('password');
    pw.type = pw.type === 'password' ? 'text' : 'password';
});
</script>

</body>
</html>