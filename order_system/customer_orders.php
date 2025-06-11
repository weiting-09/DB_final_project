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

// 撈取所有訂單與最新狀態
$sql = "
    SELECT o.order_id, o.created_at, r.name AS restaurant_name, s.status
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
    JOIN order_item od ON o.order_id = od.order_id
    JOIN menu_item m ON od.item_id = m.item_id
    JOIN restaurant r ON m.restaurant_id = r.restaurant_id
    WHERE o.customer_id = ?
    GROUP BY o.order_id, s.status
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

// 顯示每個分類
function renderOrders($title, $orders) {
    echo "<h3>$title</h3>";
    if (count($orders) === 0) {
        echo "<p>無訂單</p>";
    } else {
        echo "<ul>";
        foreach ($orders as $o) {
            echo "<li>訂單編號 #{$o['order_id']}，餐廳：{$o['restaurant_name']}，建立時間：{$o['created_at']}</li>";
        }
        echo "</ul>";
    }
}

echo "<h2>我的訂單</h2>";
renderOrders("🟡 等待商家確認 (created)", $grouped['created']);
renderOrders("🟢 商家已接受 (accepted)", $grouped['accepted']);
renderOrders("🔴 訂單已取消 (canceled)", $grouped['canceled']);
renderOrders("✅ 訂單已完成 (completed)", $grouped['completed']);
?>
