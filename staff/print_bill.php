<?php
require "../api/session.php";
requireRole('staff');
require "../api/db.php";

$id = $_GET['id'];

$stmt = $pdo->prepare("
SELECT o.*, u.name 
FROM orders o
JOIN users u ON o.user_id=u.user_id
WHERE o.order_id=?
");
$stmt->execute([$id]);
$order = $stmt->fetch();

$stmt = $pdo->prepare("
SELECT oi.*, m.item_name
FROM order_items oi
JOIN menu_items m ON oi.item_id=m.item_id
WHERE order_id=?
");
$stmt->execute([$id]);
$items = $stmt->fetchAll();
?>

<html>
<head>
<title>Bill</title>
<style>
body { font-family: Arial; }
</style>
</head>
<body onload="window.print()">

<h2>Cafeteria Bill</h2>
<p>Order ID: <?= $id ?></p>
<p>Student: <?= $order['name'] ?></p>

<hr>

<?php foreach($items as $item): ?>
<p><?= $item['item_name'] ?> x <?= $item['quantity'] ?> — ₹<?= $item['price'] ?></p>
<?php endforeach; ?>

<hr>
<h3>Total: ₹<?= $order['total_amount'] ?></h3>

</body>
</html>
