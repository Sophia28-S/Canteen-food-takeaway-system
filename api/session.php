<?php
date_default_timezone_set("Asia/Kolkata");
session_start();

function requireLogin() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: ../auth/login.php");
        exit;
    }
}

function requireRole($role) {
    requireLogin();
    if ($_SESSION['role'] !== $role) {
        header("Location: ../index.php");
        exit;
    }
}
