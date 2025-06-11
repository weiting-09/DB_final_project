<?php
require 'db.php';
session_start();

if (!isset($_SESSION['cart'])) {
    echo "沒有任何購物資料。<br><a href='menu.php'>返回</a>";
    exit;
}

$cart = $_SESSION['cart'];
$customer_id = 1; // 假設使用者 ID 為 1，你可依登入系統替換

try {
    $pdo->beginTransaction();

    foreach ($cart as $restaurant_id => $group) {
        // 建立一筆訂單
        $stmt = $pdo->prepare("INSERT INTO `order` (customer_id, restaurant_id, created_at) VALUES (?, ?, NOW())");
        $stmt->execute([$customer_id, $restaurant_id]);
        $order_id = $pdo->lastInsertId();

        // 將每個商品加入訂單明細
        foreach ($group['items'] as $item) {
            $stmt = $pdo->prepare("INSERT INTO order_item (order_id, item_id, quantity, price) VALUES (?, ?, ?, ?)");
            $stmt->execute([$order_id, $item['item_id'], $item['quantity'], $item['price']]);
        }

        // 記錄初始狀態
        $stmt = $pdo->prepare("INSERT INTO order_status_history (order_id, status, timestamp) VALUES (?, 'created', NOW())");
        $stmt->execute([$order_id]);
    }

    $pdo->commit();
    unset($_SESSION['cart']);
    echo "訂單建立成功！<br><a href='menu.php'>返回菜單</a>";
} catch (Exception $e) {
    $pdo->rollBack();
    echo "建立訂單失敗：" . htmlspecialchars($e->getMessage()) . "<br><a href='menu.php'>返回菜單</a>";
}
