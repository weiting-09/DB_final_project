<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("INSERT INTO customer (name, phone, password) VALUES (?, ?, ?)");
    $stmt->execute([$name, $phone, $password]);

    header("Location: index.php");
    exit;
}
?>

<h2>註冊新顧客</h2>
<form method="post">
    姓名: <input type="text" name="name" required><br>
    電話: <input type="text" name="phone" required><br>
    密碼: <input type="password" name="password" required><br>
    <input type="submit" value="註冊">
</form>
