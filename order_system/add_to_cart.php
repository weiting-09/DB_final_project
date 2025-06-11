<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require 'db.php'; // 包含 $pdo

// 檢查是否已登入
if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit;
}

$customer_id = $_SESSION['customer_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $quantities = $_POST['quantities'] ?? [];

    foreach ($quantities as $item_id => $qty) {
        $qty = (int)$qty;
        if ($qty > 0) {
            // 檢查是否已有此項目在購物車
            $check_sql = "SELECT quantity FROM cart_items WHERE customer_id = ? AND item_id = ?";
            $check_stmt = $pdo->prepare($check_sql);
            $check_stmt->execute([$customer_id, $item_id]);
            $existing = $check_stmt->fetch();

            if ($existing) {
                // 若已存在，更新數量
                $new_qty = $existing['quantity'] + $qty;
                $update_stmt = $pdo->prepare("UPDATE cart_items SET quantity = ? WHERE customer_id = ? AND item_id = ?");
                $update_stmt->execute([$new_qty, $customer_id, $item_id]);
            } else {
                // 若尚未存在，新增
                $insert_stmt = $pdo->prepare("INSERT INTO cart_items (customer_id, item_id, quantity) VALUES (?, ?, ?)");
                $insert_stmt->execute([$customer_id, $item_id, $qty]);
            }
        }
    }
}

// 顯示購物車內容
$sql = "
    SELECT ci.quantity, m.name AS item_name, m.price, r.name AS restaurant_name
    FROM cart_items ci
    JOIN menu_item m ON ci.item_id = m.item_id
    JOIN restaurant r ON m.restaurant_id = r.restaurant_id
    WHERE ci.customer_id = ?
";
$stmt = $pdo->prepare($sql);
$stmt->execute([$customer_id]);
$result = $stmt->fetchAll();

echo "<h2>您的購物車</h2>";
echo "<table border='1'>
    <tr>
        <th>店家</th>
        <th>商品名稱</th>
        <th>單價</th>
        <th>數量</th>
        <th>小計</th>
    </tr>";

$total = 0;
foreach ($result as $row) {
    $subtotal = $row['price'] * $row['quantity'];
    $total += $subtotal;
    echo "<tr>
        <td>{$row['restaurant_name']}</td>
        <td>{$row['item_name']}</td>
        <td>{$row['price']}</td>
        <td>{$row['quantity']}</td>
        <td>$subtotal</td>
    </tr>";
}
echo "</table>";

echo "<p><strong>總金額：$total 元</strong></p>";
echo "<a href='menu.php'>繼續選購</a> | <a href='checkout.php'>前往結帳</a>";
?>
