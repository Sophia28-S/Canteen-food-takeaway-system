<?php
require "api/db.php";

$password = password_hash("admin123", PASSWORD_DEFAULT);

$stmt = $pdo->prepare("
    INSERT INTO users (name, email, password, role)
    VALUES (?, ?, ?, ?)
");

$stmt->execute([
    "Admin",
    "admin@gmail.com",
    $password,
    "admin"
]);

echo "Admin Created!";
?>
