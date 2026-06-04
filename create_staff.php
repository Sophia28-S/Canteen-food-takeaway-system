<?php
require "api/db.php";

/* =========================
   CHANGE THESE DETAILS
========================= */

$name     = "Staff User";
$email    = "staff@gmail.com";
$password = "staff123";   // plain password

/* =========================
   HASH PASSWORD
========================= */

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

/* =========================
   INSERT STAFF
========================= */

try {

    $stmt = $pdo->prepare("
        INSERT INTO users (name, email, password, role)
        VALUES (?, ?, ?, ?)
    ");

    $stmt->execute([
        $name,
        $email,
        $hashedPassword,
        "staff"
    ]);

    echo "✅ Staff account created successfully!";

} catch (PDOException $e) {

    if ($e->errorInfo[1] == 1062) {
        echo "⚠ Email already exists!";
    } else {
        echo "Error: " . $e->getMessage();
    }
}
?>
