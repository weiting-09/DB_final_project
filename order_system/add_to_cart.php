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
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['submit_order'])) {
    if (isset($_POST['quantities'])) {
        foreach ($_POST['quantities'] as $item_id => $qty) {
            $qty = (int)$qty;
            $item_id = (int)$item_id;

            if ($qty > 0) {
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

// 處理送出訂單動作
if (isset($_POST['submit_order'])) {
    $pdo->beginTransaction();
    try {
        // 先依據不同餐廳分類商品
        $cart_stmt = $pdo->prepare("SELECT * FROM cart_items ci JOIN menu_item mi ON ci.item_id = mi.item_id WHERE ci.customer_id = ?");
        $cart_stmt->execute([$customer_id]);
        $cart_items = $cart_stmt->fetchAll();

        $grouped = [];
        foreach ($cart_items as $item) {
            $grouped[$item['restaurant_id']][] = $item;
        }

        foreach ($grouped as $restaurant_id => $items) {
            // 建立訂單
            $order_stmt = $pdo->prepare("INSERT INTO `order` (customer_id, restaurant_id, created_at) VALUES (?, ?, NOW())");
            $order_stmt->execute([$customer_id, $restaurant_id]);
            $order_id = $pdo->lastInsertId();

            // 插入 order_item
            $item_stmt = $pdo->prepare("INSERT INTO order_item (order_id, item_id, quantity, price) VALUES (?, ?, ?, ?)");
            foreach ($items as $item) {
                $item_stmt->execute([$order_id, $item['item_id'], $item['quantity'], $item['price']]);
            }

            // 插入訂單狀態 created
            $status_stmt = $pdo->prepare("INSERT INTO order_status_history (order_id, status, timestamp) VALUES (?, 'created', NOW())");
            $status_stmt->execute([$order_id]);
        }

        // 清空購物車
        $pdo->prepare("DELETE FROM cart_items WHERE customer_id = ?")->execute([$customer_id]);
        $pdo->commit();
        header("Location: customer_orders.php");
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        echo "訂單建立失敗：" . $e->getMessage();
    }
    exit;
}

// 顯示購物車內容
$sql = "
    SELECT ci.quantity, m.name AS item_name, m.price, m.item_id, r.name AS restaurant_name, r.restaurant_id
    FROM cart_items ci
    JOIN menu_item m ON ci.item_id = m.item_id
    JOIN restaurant r ON m.restaurant_id = r.restaurant_id
    WHERE ci.customer_id = ?
";
$stmt = $pdo->prepare($sql);
$stmt->execute([$customer_id]);
$items = $stmt->fetchAll();

if (empty($items)) {
    echo "<p>購物車是空的。</p>";
    echo "<a href='menu.php'>前往選購商品</a>";
    exit;
}

// 顯示購物車表格
$total = 0;
echo "<h2>您的購物車</h2>";
echo "<table border='1'>
<tr><th>店家</th><th>商品</th><th>單價</th><th>數量</th><th>小計</th><th>操作</th></tr>";
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
echo "</table><p><strong>總金額：$total 元</strong></p>";

// 提交訂單按鈕
echo "<form method='post'>
    <input type='hidden' name='submit_order' value='1'>
    <input type='submit' value='送出訂單'>
</form>";

echo "<a href='menu.php'>繼續選購</a>";
?>
