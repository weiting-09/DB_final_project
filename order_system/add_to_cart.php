<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
session_start();
require 'db.php';

if (!isset($_SESSION['customer_id'])) {
    header("Location: login.php");
    exit;
}

$customer_id = $_SESSION['customer_id'];

// 新增商品到購物車
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['quantities'])) {
        foreach ($_POST['quantities'] as $item_id => $qty) {
            $qty = (int)$qty;
            $item_id = (int)$item_id;

            if ($qty > 0) {
                // 檢查是否已存在該商品
                $check = $pdo->prepare("SELECT quantity FROM cart_items WHERE customer_id = ? AND item_id = ?");
                $check->execute([$customer_id, $item_id]);
                $existing = $check->fetch();

                if ($existing) {
                    $new_qty = $existing['quantity'] + $qty;
                    $update = $pdo->prepare("UPDATE cart_items SET quantity = ? WHERE customer_id = ? AND item_id = ?");
                    $update->execute([$new_qty, $customer_id, $item_id]);
                } else {
                    $insert = $pdo->prepare("INSERT INTO cart_items (customer_id, item_id, quantity) VALUES (?, ?, ?)");
                    $insert->execute([$customer_id, $item_id, $qty]);
                }
            }
        }
    }

    // 處理增加、減少、刪除
    if (isset($_POST['action'], $_POST['item_id'])) {
        $item_id = (int)$_POST['item_id'];
        if ($_POST['action'] === 'add') {
            $pdo->prepare("UPDATE cart_items SET quantity = quantity + 1 WHERE customer_id = ? AND item_id = ?")
                ->execute([$customer_id, $item_id]);
        } elseif ($_POST['action'] === 'minus') {
            $pdo->prepare("UPDATE cart_items SET quantity = quantity - 1 WHERE customer_id = ? AND item_id = ? AND quantity > 1")
                ->execute([$customer_id, $item_id]);
        } elseif ($_POST['action'] === 'delete') {
            $pdo->prepare("DELETE FROM cart_items WHERE customer_id = ? AND item_id = ?")
                ->execute([$customer_id, $item_id]);
        }
    }
}

// 顯示購物車
$sql = "
    SELECT ci.quantity, m.name AS item_name, m.price, m.item_id, r.name AS restaurant_name
    FROM cart_items ci
    JOIN menu_item m ON ci.item_id = m.item_id
    JOIN restaurant r ON m.restaurant_id = r.restaurant_id
    WHERE ci.customer_id = ?
";
$stmt = $pdo->prepare($sql);
$stmt->execute([$customer_id]);
$items = $stmt->fetchAll();

echo "<h2>您的購物車</h2>";
echo "<table border='1'>
<tr>
    <th>店家</th>
    <th>商品</th>
    <th>單價</th>
    <th>數量</th>
    <th>小計</th>
    <th>操作</th>
</tr>";

$total = 0;
foreach ($items as $row) {
    $subtotal = $row['price'] * $row['quantity'];
    $total += $subtotal;
    echo "<tr>
        <td>{$row['restaurant_name']}</td>
        <td>{$row['item_name']}</td>
        <td>{$row['price']}</td>
        <td>{$row['quantity']}</td>
        <td>$subtotal</td>
        <td>
            <form method='post' style='display:inline'>
                <input type='hidden' name='item_id' value='{$row['item_id']}'>
                <button type='submit' name='action' value='add'>＋</button>
                <button type='submit' name='action' value='minus'>－</button>
                <button type='submit' name='action' value='delete'>刪除</button>
            </form>
        </td>
    </tr>";
}
echo "</table>";
echo "<p><strong>總金額：$total 元</strong></p>";
echo "<a href='menu.php'>繼續選購</a> | <a href='checkout.php'>前往結帳</a>";
?>
