<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require 'db.php';

if (!isset($_SESSION['restaurant_id'])) {
    header("Location: login.php");
    exit;
}
$restaurant_id = $_SESSION['restaurant_id'];

// 更新餐廳資訊
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_info'])) {
    $name = $_POST['name'];
    $location = $_POST['location'];
    $phone = $_POST['phone'];
    $stmt = $pdo->prepare("UPDATE restaurant SET name = ?, location = ?, phone = ? WHERE restaurant_id = ?");
    $stmt->execute([$name, $location, $phone, $restaurant_id]);
    echo "<p style='color:green;'>餐廳資訊已更新。</p>";
}

// 新增菜單項目
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_item'])) {
    $item_name = $_POST['item_name'];
    $price = $_POST['price'];
    $stmt = $pdo->prepare("INSERT INTO menu_item (restaurant_id, name, price) VALUES (?, ?, ?)");
    $stmt->execute([$restaurant_id, $item_name, $price]);
    echo "<p style='color:green;'>菜單項目已新增。</p>";
}

// 修改菜單項目
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_item'])) {
    $item_id = $_POST['item_id'];
    $new_name = $_POST['new_name'];
    $new_price = $_POST['new_price'];
    $stmt = $pdo->prepare("UPDATE menu_item SET name = ?, price = ? WHERE item_id = ? AND restaurant_id = ?");
    $stmt->execute([$new_name, $new_price, $item_id, $restaurant_id]);
    echo "<p style='color:green;'>菜單項目已更新。</p>";
}

// 訂單狀態更新
if ($_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['order_id'], $_POST['action']) &&
    in_array($_POST['action'], ['accepted', 'canceled', 'completed'])) {

    $order_id = $_POST['order_id'];
    $action = $_POST['action'];

    // 檢查是否已經有該 order_id + status 的紀錄
    $checkStmt = $pdo->prepare("SELECT 1 FROM order_status_history WHERE order_id = ? AND status = ?");
    $checkStmt->execute([$order_id, $action]);
    $exists = $checkStmt->fetch();

    if (!$exists) {
        $insertStmt = $pdo->prepare("INSERT INTO order_status_history (order_id, status, timestamp) VALUES (?, ?, NOW())");
        $insertStmt->execute([$order_id, $action]);
        echo "<p style='color:green;'>訂單 {$order_id} 狀態已更新為 {$action}。</p>";
    } else {
        echo "<p style='color:orange;'>訂單 {$order_id} 已經是 {$action} 狀態，無需重複更新。</p>";
    }
}


// 取得餐廳資訊
$stmt = $pdo->prepare("SELECT * FROM restaurant WHERE restaurant_id = ?");
$stmt->execute([$restaurant_id]);
$restaurant = $stmt->fetch(PDO::FETCH_ASSOC);

// 取得菜單項目
$stmt = $pdo->prepare("SELECT * FROM menu_item WHERE restaurant_id = ?");
$stmt->execute([$restaurant_id]);
$menu_items = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 取得訂單與最新狀態
$order_sql = "
    SELECT o.order_id, c.name AS customer_name,
           SUM(oi.quantity * mi.price) AS total_price,
           osh.status
    FROM `order` o
    JOIN customer c ON o.customer_id = c.customer_id
    JOIN order_item oi ON o.order_id = oi.order_id
    JOIN menu_item mi ON oi.item_id = mi.item_id
    JOIN (
        SELECT osh1.order_id, osh1.status
        FROM order_status_history osh1
        INNER JOIN (
            SELECT order_id, MAX(timestamp) AS max_time
            FROM order_status_history
            GROUP BY order_id
        ) latest_osh ON osh1.order_id = latest_osh.order_id AND osh1.timestamp = latest_osh.max_time
    ) osh ON o.order_id = osh.order_id
    WHERE o.restaurant_id = ?
    GROUP BY o.order_id, c.name, osh.status
    ORDER BY osh.status, o.order_id
";
$stmt = $pdo->prepare($order_sql);
$stmt->execute([$restaurant_id]);
$orders = $stmt->fetchAll(PDO::FETCH_ASSOC);

// 分類訂單
$categorized_orders = ['created' => [], 'accepted' => [], 'canceled' => [], 'completed' => []];
foreach ($orders as $order) {
    $categorized_orders[$order['status']][] = $order;
}

// 顯示訂單
function render_orders($orders, $status) {
    if (empty($orders)) {
        echo "<p>目前無「{$status}」狀態的訂單。</p>";
        return;
    }
    echo "<table border='1'><tr>
        <th>訂單編號</th><th>顧客</th><th>總金額</th><th>狀態</th>";
    if ($status === 'created' || $status === 'accepted') echo "<th>操作</th>";
    echo "</tr>";
    foreach ($orders as $order) {
        echo "<tr>
            <td>{$order['order_id']}</td>
            <td>" . htmlspecialchars($order['customer_name']) . "</td>
            <td>{$order['total_price']}</td>
            <td>{$order['status']}</td>";
        if ($status === 'created') {
            echo "<td>
                <form method='post' style='display:inline'>
                    <input type='hidden' name='order_id' value='{$order['order_id']}'>
                    <input type='hidden' name='action' value='accepted'>
                    <input type='submit' value='接收'>
                </form>
                <form method='post' style='display:inline'>
                    <input type='hidden' name='order_id' value='{$order['order_id']}'>
                    <input type='hidden' name='action' value='canceled'>
                    <input type='submit' value='取消'>
                </form>
            </td>";
        } elseif ($status === 'accepted') {
            echo "<td>
                <form method='post' style='display:inline'>
                    <input type='hidden' name='order_id' value='{$order['order_id']}'>
                    <input type='hidden' name='action' value='completed'>
                    <input type='submit' value='完成'>
                </form>
            </td>";
        }
        echo "</tr>";
    }
    echo "</table><br>";
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>餐廳後台管理</title>
</head>
<body>
<h1>餐廳後台管理</h1>

<h2>餐廳資訊</h2>
<form method="post">
    名稱：<input type="text" name="name" value="<?= htmlspecialchars($restaurant['name']) ?>"><br>
    地點：<input type="text" name="location" value="<?= htmlspecialchars($restaurant['location']) ?>"><br>
    電話：<input type="text" name="phone" value="<?= htmlspecialchars($restaurant['phone']) ?>"><br>
    <input type="submit" name="update_info" value="更新餐廳資訊">
</form>

<hr>

<h2>菜單管理</h2>
<table border="1">
    <tr><th>名稱</th><th>價格</th><th>操作</th></tr>
    <?php foreach ($menu_items as $item): ?>
        <tr>
            <form method="post">
                <td><input type="text" name="new_name" value="<?= htmlspecialchars($item['name']) ?>"></td>
                <td><input type="number" step="0.1" name="new_price" value="<?= $item['price'] ?>"></td>
                <td>
                    <input type="hidden" name="item_id" value="<?= $item['item_id'] ?>">
                    <input type="submit" name="edit_item" value="修改">
                </td>
            </form>
        </tr>
    <?php endforeach; ?>
</table>

<h3>新增菜單項目</h3>
<form method="post">
    名稱：<input type="text" name="item_name" required>
    價格：<input type="number" step="0.1" name="price" required>
    <input type="submit" name="add_item" value="新增">
</form>

<hr>

<h2>訂單管理</h2>

<h3>等待確認的訂單 (created)</h3>
<?php render_orders($categorized_orders['created'], 'created'); ?>

<h3>已接收的訂單 (accepted)</h3>
<?php render_orders($categorized_orders['accepted'], 'accepted'); ?>

<h3>已完成的訂單 (completed)</h3>
<?php render_orders($categorized_orders['completed'], 'completed'); ?>

<h3>已取消的訂單 (canceled)</h3>
<?php render_orders($categorized_orders['canceled'], 'canceled'); ?>

</body>
</html>
