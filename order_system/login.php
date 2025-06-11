<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $role = $_POST['role'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];

    if ($role === 'customer') {
        $stmt = $pdo->prepare("SELECT customer_id FROM customer WHERE phone = ? AND password = ?");
        $stmt->execute([$phone, $password]);
        $user = $stmt->fetch();
        if ($user) {
            $_SESSION['customer_id'] = $user['customer_id'];
            header("Location: add_to_cart.php");
            exit;
        }
    } elseif ($role === 'restaurant') {
        $stmt = $pdo->prepare("SELECT restaurant_id FROM restaurant WHERE phone = ? AND password = ?");
        $stmt->execute([$phone, $password]);
        $user = $stmt->fetch();
        if ($user) {
            $_SESSION['restaurant_id'] = $user['restaurant_id'];
            header("Location: restaurant_dashboard.php");
            exit;
        }
    }

    echo "登入失敗，請檢查電話、密碼與身份類型";
}
?>

<h2>登入</h2>
<form method="post">
    身份:
    <select name="role" required>
        <option value="customer">顧客</option>
        <option value="restaurant">商家</option>
    </select><br>
    電話: <input type="text" name="phone" required><br>
    密碼: <input type="password" name="password" required><br>
    <input type="submit" value="登入">
</form>
