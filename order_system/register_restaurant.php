<?php
session_start();
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $location = $_POST['location'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("INSERT INTO restaurant (name, location, phone, password) VALUES (?, ?, ?, ?)");
    $stmt->execute([$name, $location, $phone, $password]);

    header("Location: login.php");
    exit;
}
?>

<h2>註冊新商家</h2>
<form method="post">
    餐廳名稱: <input type="text" name="name" required><br>
    地點: <input type="text" name="location" required><br>
    電話: <input type="text" name="phone" required><br>
    密碼: <input type="password" name="password" required><br>
    <input type="submit" value="註冊">
</form>
