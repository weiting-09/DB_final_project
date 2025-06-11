<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require 'db.php'; // 使用 PDO 連線的檔案

if (!isset($pdo)) {
    exit("PDO 連線物件未定義");
}


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone = $_POST['phone'];
    $password = $_POST['password'];

    // 查詢顧客資料
    $stmt = $pdo->prepare("SELECT customer_id FROM customer WHERE phone = ? AND password = ?");
    $stmt->execute([$phone, $password]);
    $user = $stmt->fetch();

    if ($user) {
        $_SESSION['customer_id'] = $user['customer_id'];
        header("Location: add_to_cart.php"); // 登入成功後導向購物車頁
        exit;
    } else {
        echo "登入失敗，請檢查電話與密碼";
    }
}
?>

<!-- 登入表單 -->
<form method="post">
    電話: <input type="text" name="phone" required><br>
    密碼: <input type="password" name="password" required><br>
    <input type="submit" value="登入">
</form>
