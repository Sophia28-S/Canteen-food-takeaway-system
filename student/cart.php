<?php
require "../api/session.php";
requireRole('student');
require "../api/db.php";

if(!isset($_SESSION['cart'])){
    $_SESSION['cart'] = [];
}

/* ADD TO CART */
if(isset($_POST['add_cart'])){
    $item_id = (int)$_POST['item_id'];
    $qty = (int)$_POST['quantity'];

    if(isset($_SESSION['cart'][$item_id])){
        $_SESSION['cart'][$item_id] += $qty;
    } else {
        $_SESSION['cart'][$item_id] = $qty;
    }

    header("Location: cart.php");
    exit;
}

/* REMOVE ITEM */
if(isset($_GET['remove'])){
    unset($_SESSION['cart'][$_GET['remove']]);
}

/* PLACE ORDER */
if(isset($_POST['place_order'])){

    $user_id = $_SESSION['user_id'];
    $order_type = $_POST['order_type'];
    $order_type = $_POST['order_type'];

if($order_type == "now"){
    
    $stmt = $pdo->prepare("SELECT slot_id FROM pickup_slots WHERE is_active=1 ORDER BY slot_id ASC LIMIT 1");
    $stmt->execute();
    $slot = $stmt->fetch();

    $slot_id = $slot ? $slot['slot_id'] : NULL;

}else{

    $slot_id = (int)$_POST['slot_id'];

}

    /* Validate slot */
    if($order_type == "scheduled" && !$slot_id){
        die("Please select a pickup slot.");
    }
    $payment_method = $_POST['payment_method'];

    $total = 0;

    foreach($_SESSION['cart'] as $item_id => $qty){
        $stmt = $pdo->prepare("SELECT price, stock FROM menu_items WHERE item_id=?");
        $stmt->execute([$item_id]);
        $item = $stmt->fetch();

        if(!$item || $qty > $item['stock']){
            die("Stock not available");
        }

        $total += $item['price'] * $qty;
    }

    if($slot_id){
    $stmt = $pdo->prepare("SELECT * FROM pickup_slots WHERE slot_id=? AND is_active=1");
    $stmt->execute([$slot_id]);
    if(!$stmt->fetch()){
        die("Invalid pickup slot selected.");
    }
}

    /* SETTINGS */
    $stmt = $pdo->prepare("SELECT * FROM system_settings WHERE id=1");
    $stmt->execute();
    $settings = $stmt->fetch();

    $tax = ($total * $settings['tax_percentage']) / 100;
    $service = ($total * $settings['service_charge']) / 100;
    $grand = $total + $tax + $service;

    /* INSERT ORDER */
    $stmt = $pdo->prepare("
        INSERT INTO orders 
        (user_id, slot_id, total_amount, payment_method, order_type) 
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->execute([$user_id, $slot_id, $grand, $payment_method, $order_type]);
    $order_id = $pdo->lastInsertId();

    /* INSERT ORDER ITEMS */
    foreach($_SESSION['cart'] as $item_id => $qty){
        $stmt = $pdo->prepare("SELECT price FROM menu_items WHERE item_id=?");
        $stmt->execute([$item_id]);
        $item = $stmt->fetch();

        $stmt = $pdo->prepare("
            INSERT INTO order_items (order_id, item_id, quantity, price)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$order_id, $item_id, $qty, $item['price']]);

        $pdo->prepare("UPDATE menu_items SET stock = stock - ? WHERE item_id=?")
            ->execute([$qty, $item_id]);
    }

    unset($_SESSION['cart']);
    header("Location: payment.php?order_id=".$order_id);
    exit;
}

/* FETCH CART ITEMS */
$cartItems = [];
$total = 0;

foreach($_SESSION['cart'] as $item_id => $qty){
    $stmt = $pdo->prepare("SELECT * FROM menu_items WHERE item_id=?");
    $stmt->execute([$item_id]);
    $item = $stmt->fetch();
    if($item){
        $item['qty'] = $qty;
        $item['subtotal'] = $qty * $item['price'];
        $total += $item['subtotal'];
        $cartItems[] = $item;
    }
}

/* FETCH ACTIVE SLOTS */
$stmt = $pdo->prepare("SELECT * FROM pickup_slots WHERE is_active=1 ORDER BY slot_id ASC");
$stmt->execute();
$slots = $stmt->fetchAll();

$currentTime = time();
$currentSlot = null;

foreach($slots as $s){
    $times = explode("-", $s['slot_time']);
    $start = strtotime(trim($times[0]));
    $end = strtotime(trim($times[1]));
    if($currentTime >= $start && $currentTime <= $end){
        $currentSlot = $s['slot_id'];
        break;
    }
}

if(!$currentSlot){
    foreach($slots as $s){
        $times = explode("-", $s['slot_time']);
        $start = strtotime(trim($times[0]));
        if($start > $currentTime){
            $currentSlot = $s['slot_id'];
            break;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Cart</title>
<link rel="stylesheet" href="../assets/css/main.css">
<link rel="stylesheet" href="../assets/css/student.css">
<link rel="stylesheet" href="../assets/css/dashboard.css">
<link rel="stylesheet" href="../assets/css/cart.css">
</head>
<body>
<?php include "../includes/navbar.php"; ?>

<div class="dashboard-container">
<?php include "../includes/sidebar_student.php"; ?>

<div class="container">
    <h2>Your Cart</h2>

    <?php if(empty($cartItems)): ?>

    <!-- EMPTY CART -->
    <div class="cart-empty">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.4" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z"/>
        </svg>
        <strong>Your cart is empty</strong>
        <span>Browse the menu and add something delicious!</span>
        <a href="menu.php" class="cart-empty-btn">Go to Menu</a>
    </div>

    <?php else: ?>

    <!-- CART LAYOUT -->
    <div class="cart-layout">

        <!-- LEFT: Item Cards -->
        <div class="cart-items-panel">

            <div class="cart-items-header">
                <span><?= count($cartItems) ?> item<?= count($cartItems) > 1 ? 's' : '' ?> in your cart</span>
                <a href="menu.php" class="add-more-link">+ Add more</a>
            </div>

            <div class="cart-items-list">
            <?php foreach($cartItems as $index => $item): ?>
            <div class="cart-item-card" style="animation-delay: <?= $index * 0.06 ?>s">

                <!-- Item colour accent -->
                <div class="cart-item-accent"></div>

                <div class="cart-item-body">
                    <div class="cart-item-info">
                        <div class="cart-item-name"><?= htmlspecialchars($item['item_name']) ?></div>
                        <div class="cart-item-unit">₹<?= number_format($item['price'], 2) ?> × <?= $item['qty'] ?></div>
                    </div>

                    <div class="cart-item-right">
                        <div class="cart-item-subtotal">₹<?= number_format($item['subtotal'], 2) ?></div>
                        <a href="?remove=<?= $item['item_id'] ?>" class="cart-remove-btn" title="Remove">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </a>
                    </div>
                </div>

            </div>
            <?php endforeach; ?>
            </div>

        </div>

        <!-- RIGHT: Order Summary + Form -->
        <div class="cart-summary-panel">

            <div class="summary-card">
                <div class="summary-title">Order Summary</div>

                <div class="summary-row">
                    <span>Subtotal</span>
                    <span>₹<?= number_format($total, 2) ?></span>
                </div>
                <div class="summary-divider"></div>
                <div class="summary-row summary-total">
                    <span>Total</span>
                    <span>₹<?= number_format($total, 2) ?></span>
                </div>
            </div>

            <!-- ORDER FORM -->
            <form method="POST" class="order-form">

                <!-- Order Type -->
                <div class="form-field">
                    <label class="field-label">Order Type</label>
                    <div class="select-wrap">
                        <select name="order_type" id="order_type" required>
                            <option value="now">🕐 Order Now</option>
                            <option value="scheduled">📅 Scheduled</option>
                        </select>
                        <svg class="select-arrow" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                    </div>
                </div>

                <!-- Pickup Slot -->
                <div class="form-field" id="slot_field">
                    <label class="field-label">Pickup Slot</label>
                    <div class="select-wrap">
                        <select name="slot_id" id="slot_select">
                            <?php foreach($slots as $s): ?>
                            <option value="<?= $s['slot_id'] ?>" <?= $s['slot_id']==$currentSlot ? "selected" : "" ?>>
                                <?= htmlspecialchars($s['slot_time']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                        <svg class="select-arrow" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/></svg>
                    </div>
                </div>

                <!-- Payment -->
                <div class="form-field">
                    <label class="field-label">Payment Method</label>
                    <div class="payment-options">
                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="cash" checked>
                            <span class="payment-option-box">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z"/></svg>
                                Cash
                            </span>
                        </label>
                        <label class="payment-option">
                            <input type="radio" name="payment_method" value="online">
                            <span class="payment-option-box">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/></svg>
                                Online
                            </span>
                        </label>
                    </div>
                </div>

                <button type="submit" name="place_order" class="place-order-btn">
                    Place Order
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12h15m0 0l-6.75-6.75M19.5 12l-6.75 6.75"/></svg>
                </button>

            </form>

        </div>
    </div>

    <?php endif; ?>
</div>
</div>

<script>
const orderType  = document.getElementById("order_type");
const slotSelect = document.getElementById("slot_select");
const slotField  = document.getElementById("slot_field");

function toggleSlot(){
    if(orderType.value === "now"){
        slotSelect.disabled = true;
        slotField.classList.add("field-disabled");
    } else {
        slotSelect.disabled = false;
        slotField.classList.remove("field-disabled");
    }
}

orderType.addEventListener("change", toggleSlot);
toggleSlot();

/* Payment radio visual */
document.querySelectorAll('.payment-option input[type="radio"]').forEach(radio => {
    radio.addEventListener("change", () => {
        document.querySelectorAll('.payment-option-box').forEach(b => b.classList.remove("selected"));
        if(radio.checked) radio.nextElementSibling.classList.add("selected");
    });
});
/* init */
document.querySelector('.payment-option input[type="radio"]:checked')
    ?.nextElementSibling.classList.add("selected");
</script>
</body>
</html>