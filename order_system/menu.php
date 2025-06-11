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

// 取得所有菜單項目與其店家名稱
$sql = "
    SELECT m.item_id, m.name AS item_name, m.price, r.name AS restaurant_name 
    FROM menu_item m 
    JOIN restaurant r ON m.restaurant_id = r.restaurant_id
";
$result = $pdo->query($sql);
echo "<button onclick=\"location.href='customer_orders.php'\">我的訂單</button>";
?>

<h2>所有菜單項目</h2>
<form method="post" action="add_to_cart.php">
<table border="1">
    <tr>
        <th>店家名稱</th>
        <th>商品名稱</th>
        <th>價格</th>
        <th>數量</th>
    </tr>
    <?php while ($row = $result->fetch(PDO::FETCH_ASSOC)): ?>
    <tr>
        <td><?= htmlspecialchars($row['restaurant_name']) ?></td>
        <td><?= htmlspecialchars($row['item_name']) ?></td>
        <td><?= $row['price'] ?></td>
        <td>
            <input type="number" name="quantities[<?= $row['item_id'] ?>]" value="0" min="0">
        </td>
    </tr>
    <?php endwhile; ?>
</table>
<br>
<input type="submit" value="加入購物車">
</form>
