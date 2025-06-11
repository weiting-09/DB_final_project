<?php
session_start();
require 'db.php'; // 這裡 $pdo 已是 PDO 物件

// 假設商家登入後已儲存 restaurant_id 在 session
if (!isset($_SESSION['restaurant_id'])) {
    header("Location: login.php");
    exit;
}
$restaurant_id = $_SESSION['restaurant_id'];

// 更新餐廳資訊
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_restaurant'])) {
    $name = $_POST['name'] ?? '';
    $location = $_POST['location'] ?? '';

    $update_sql = "UPDATE restaurant SET name = ?, location = ? WHERE restaurant_id = ?";
    $stmt = $pdo->prepare($update_sql);
    $stmt->execute([$name, $location, $restaurant_id]);
    echo "<p style='color:green;'>餐廳資訊已更新</p>";
}

// 取得餐廳資料
$stmt = $pdo->prepare("SELECT * FROM restaurant WHERE restaurant_id = ?");
$stmt->execute([$restaurant_id]);
$restaurant = $stmt->fetch();

// 取得菜單項目
$stmt = $pdo->prepare("SELECT * FROM menu_item WHERE restaurant_id = ?");
$stmt->execute([$restaurant_id]);
$menu_items = $stmt->fetchAll();

// 取得尚未完成的訂單及最新狀態
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
    WHERE o.restaurant_id = ? AND osh.status != 'completed'
    GROUP BY o.order_id, c.name, osh.status
";
$stmt = $pdo->prepare($order_sql);
$stmt->execute([$restaurant_id]);
$orders = $stmt->fetchAll();

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>餐廳管理後台</title>
</head>
<body>
    <h1>餐廳管理後台</h1>

    <h2>餐廳資訊</h2>
    <form method="post">
        <input type="hidden" name="update_restaurant" value="1">
        <label>名稱：<input type="text" name="name" value="<?= htmlspecialchars($restaurant['name']) ?>" required></label><br>
        <label>地址：<input type="text" name="location" value="<?= htmlspecialchars($restaurant['location']) ?>" required></label><br>
        <input type="submit" value="更新餐廳資訊">
    </form>

    <h2>菜單項目</h2>
    <table border="1" cellpadding="5" cellspacing="0">
        <tr><th>名稱</th><th>價格</th><th>操作</th></tr>
        <?php foreach ($menu_items as $item): ?>
        <tr>
            <td><?= htmlspecialchars($item['name']) ?></td>
            <td><?= htmlspecialchars($item['price']) ?></td>
            <td>
                <a href="edit_menu_item.php?item_id=<?= $item['item_id'] ?>">編輯</a>
                <!-- 你也可以新增刪除功能 -->
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    <p><a href="add_menu_item.php">新增菜單項目</a></p>

    <h2>尚未完成的訂單</h2>
    <?php if (count($orders) === 0): ?>
        <p>目前無尚未完成的訂單。</p>
    <?php else: ?>
    <table border="1" cellpadding="5" cellspacing="0">
        <tr>
            <th>訂單編號</th>
            <th>顧客名稱</th>
            <th>總金額</th>
            <th>狀態</th>
        </tr>
        <?php foreach ($orders as $order): ?>
        <tr>
            <td><?= $order['order_id'] ?></td>
            <td><?= htmlspecialchars($order['customer_name']) ?></td>
            <td><?= number_format($order['total_price'], 2) ?></td>
            <td><?= htmlspecialchars($order['status']) ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
    <?php endif; ?>

</body>
</html>
