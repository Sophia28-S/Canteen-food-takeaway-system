<?php
require "../api/session.php";
requireRole('staff');
require "../api/db.php";

/* =============================================
   CHECK if accepted_at column exists
   If not, add it automatically
   ============================================= */
try {
    $pdo->query("SELECT accepted_at FROM orders LIMIT 1");
} catch (PDOException $e) {
    /* Column doesn't exist — add it */
    $pdo->exec("ALTER TABLE orders ADD COLUMN accepted_at DATETIME NULL DEFAULT NULL");
}

/* =============================================
   AUTO-PROGRESSION (runs on every request)
   accepted_at + 5 min  → preparing
   accepted_at + 10 min → ready
   ============================================= */
try {
    $pdo->prepare("
        UPDATE orders
        SET status = 'preparing'
        WHERE status = 'accepted'
        AND accepted_at IS NOT NULL
        AND accepted_at <= NOW() - INTERVAL 5 MINUTE
    ")->execute();

    $pdo->prepare("
        UPDATE orders
        SET status = 'ready'
        WHERE status = 'preparing'
        AND accepted_at IS NOT NULL
        AND accepted_at <= NOW() - INTERVAL 10 MINUTE
    ")->execute();
} catch (PDOException $e) {
    /* Silent fail */
}

/* =============================================
   HANDLE MANUAL ACTIONS
   ============================================= */
if(isset($_POST['action'])){

    $orderId = (int)$_POST['order_id'];
    $action  = $_POST['action'];

    $statusMap = [
        "cancel"   => "cancelled",
        "prepare"  => "preparing",
        "ready"    => "ready",
        "complete" => "completed"
    ];

    /* ACCEPT — stamp accepted_at with fallback */
    if($action === "accept"){
        try {
            $stmt = $pdo->prepare("
                UPDATE orders
                SET status = 'accepted',
                    accepted_at = NOW()
                WHERE order_id = ?
            ");
            $stmt->execute([$orderId]);
        } catch (PDOException $e) {
            /* Column still missing somehow — status only */
            $stmt = $pdo->prepare("UPDATE orders SET status = 'accepted' WHERE order_id = ?");
            $stmt->execute([$orderId]);
        }
    }

    /* OTHER STATUS UPDATES */
    elseif(isset($statusMap[$action])){
        $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE order_id = ?");
        $stmt->execute([$statusMap[$action], $orderId]);
    }

    /* VIEW ORDER ITEMS */
    elseif($action === "view"){
        $stmt = $pdo->prepare("
            SELECT oi.*, m.item_name
            FROM order_items oi
            JOIN menu_items m ON oi.item_id = m.item_id
            WHERE oi.order_id = ?
        ");
        $stmt->execute([$orderId]);
        $items = $stmt->fetchAll();

        foreach($items as $item){
            echo "<p>{$item['item_name']} x {$item['quantity']} (₹{$item['price']})</p>";
        }
    }

}
?>