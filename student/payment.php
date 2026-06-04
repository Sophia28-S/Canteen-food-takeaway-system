<?php
require "../api/session.php";
requireRole('student');
require "../api/db.php";

$order_id = $_GET['order_id'] ?? 0;

/* FETCH ORDER */
$stmt = $pdo->prepare("SELECT * FROM orders WHERE order_id=? AND user_id=?");
$stmt->execute([$order_id, $_SESSION['user_id']]);
$order = $stmt->fetch();

if(!$order){
    die("Invalid order.");
}

/* FAKE PAYMENT CONFIRM */
if(isset($_POST['confirm_payment'])){
    $stmt = $pdo->prepare("
        UPDATE orders 
        SET payment_status='paid'
        WHERE order_id=?
    ");
    $stmt->execute([$order_id]);

    header("Location: order_history.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Payment</title>
<link rel="stylesheet" href="../assets/css/main.css">
<link rel="stylesheet" href="../assets/css/student.css">
<link rel="stylesheet" href="../assets/css/dashboard.css">
<link rel="stylesheet" href="../assets/css/payment.css">
</head>
<body>

<?php include "../includes/navbar.php"; ?>

<div class="payment-page">

    <div class="payment-card">

        <!-- Header -->
        <div class="payment-header">
            <div class="payment-header-icon">
                <?php if($order['payment_method'] === 'cash'): ?>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z"/>
                </svg>
                <?php else: ?>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 6.75h.75v.75h-.75v-.75zM6.75 16.5h.75v.75h-.75v-.75zM16.5 6.75h.75v.75h-.75v-.75zM13.5 13.5h.75v.75h-.75v-.75zM13.5 19.5h.75v.75h-.75v-.75zM19.5 13.5h.75v.75h-.75v-.75zM19.5 19.5h.75v.75h-.75v-.75zM16.5 16.5h.75v.75h-.75v-.75z"/>
                </svg>
                <?php endif; ?>
            </div>

            <div class="payment-header-text">
                <h2><?= $order['payment_method'] === 'cash' ? 'Cash Payment' : 'Scan & Pay' ?></h2>
                <p><?= $order['payment_method'] === 'cash' ? 'Pay at the counter when you pick up' : 'Scan the QR with any UPI app' ?></p>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="payment-summary">
            <div class="summary-row">
                <span class="summary-label">Order ID</span>
                <span class="summary-value">#<?= $order_id ?></span>
            </div>
            <div class="summary-row">
                <span class="summary-label">Payment Method</span>
                <span class="method-badge <?= $order['payment_method'] ?>">
                    <?= ucfirst($order['payment_method']) ?>
                </span>
            </div>
            <div class="summary-divider"></div>
            <div class="summary-row summary-total">
                <span class="summary-label">Amount Due</span>
                <span class="summary-amount">₹<?= number_format($order['total_amount'], 2) ?></span>
            </div>
        </div>

        <!-- CASH: instruction block -->
        <?php if($order['payment_method'] === 'cash'): ?>
        <div class="cash-block">
            <div class="cash-step">
                <div class="cash-step-num">1</div>
                <span>Collect your order from the canteen counter</span>
            </div>
            <div class="cash-step">
                <div class="cash-step-num">2</div>
                <span>Show your Order ID <strong>#<?= $order_id ?></strong> to the staff</span>
            </div>
            <div class="cash-step">
                <div class="cash-step-num">3</div>
                <span>Pay <strong>₹<?= number_format($order['total_amount'], 2) ?></strong> in cash at the counter</span>
            </div>
        </div>

        <!-- ONLINE: QR block -->
        <?php else: ?>
        <div class="qr-block">
            <div class="qr-wrap">
                <img src="../assets/images/fake_qr.png" alt="UPI QR Code">
            </div>
            <p class="qr-hint">Point your camera or UPI app at the code above</p>
            <div class="upi-apps">
                <span>GPay</span>
                <span>PhonePe</span>
                <span>Paytm</span>
                <span>BHIM</span>
            </div>
        </div>
        <?php endif; ?>

        <!-- Confirm Button -->
        <form method="POST" class="payment-form">
            <button name="confirm_payment" class="confirm-btn">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                </svg>
                <?= $order['payment_method'] === 'cash' ? 'Confirm Order' : 'I Have Paid' ?>
            </button>
        </form>

    </div>

</div>

</body>
</html>