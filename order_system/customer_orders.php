<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require 'db.php';

if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit;
}

$customer_id = $_SESSION['customer_id'];

// 撈取所有訂單與最新狀態與總金額
$sql = "
    SELECT o.order_id, o.created_at, r.name AS restaurant_name,
           SUM(oi.quantity * mi.price) AS total_price,
           s.status
    FROM `order` o
    JOIN (
        SELECT osh1.order_id, osh1.status
        FROM order_status_history osh1
        JOIN (
            SELECT order_id, MAX(timestamp) AS latest_time
            FROM order_status_history
            GROUP BY order_id
        ) osh2 ON osh1.order_id = osh2.order_id AND osh1.timestamp = osh2.latest_time
    ) s ON o.order_id = s.order_id
    JOIN order_item oi ON o.order_id = oi.order_id
    JOIN menu_item mi ON oi.item_id = mi.item_id
    JOIN restaurant r ON mi.restaurant_id = r.restaurant_id
    WHERE o.customer_id = ?
    GROUP BY o.order_id, s.status, r.name, o.created_at
    ORDER BY o.created_at DESC
";
$stmt = $pdo->prepare($sql);
$stmt->execute([$customer_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 依狀態分類
$grouped = [
    'created' => [],
    'accepted' => [],
    'canceled' => [],
    'completed' => []
];

foreach ($orders as $order) {
    $grouped[$order['status']][] = $order;
}

// 顯示訂單表格函式
function renderOrdersTable($title, $orders) {
    echo "<h3>$title</h3>";
    if (count($orders) === 0) {
        echo "<p>無訂單</p>";
        return;
    }
    echo "<table border='1' cellpadding='5' cellspacing='0'>";
    echo "<tr>
            <th>訂單編號</th>
            <th>餐廳名稱</th>
            <th>建立時間</th>
            <th>總金額</th>
            <th>狀態</th>
          </tr>";
    foreach ($orders as $o) {
        $created = htmlspecialchars($o['created_at']);
        $rest_name = htmlspecialchars($o['restaurant_name']);
        $status = htmlspecialchars($o['status']);
        $order_id = (int)$o['order_id'];
        $total = number_format($o['total_price'], 2);
        echo "<tr>
                <td>#{$order_id}</td>
                <td>{$rest_name}</td>
                <td>{$created}</td>
                <td>NT$ {$total}</td>
                <td>{$status}</td>
              </tr>";
    }
    echo "</table><br>";
}

echo "<h2>我的訂單</h2>";
renderOrdersTable("等待商家確認 (created)", $grouped['created']);
renderOrdersTable("商家已接受 (accepted)", $grouped['accepted']);
renderOrdersTable("訂單已取消 (canceled)", $grouped['canceled']);
renderOrdersTable("訂單已完成 (completed)", $grouped['completed']);

echo "<button onclick=\"location.href='add_to_cart.php'\">返回購物車</button>";
?>
