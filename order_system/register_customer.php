<form method="post" action="register_customer.php">
    電話：<input type="text" name="phone"><br>
    密碼：<input type="password" name="password"><br>
    姓名：<input type="text" name="name"><br>
    <input type="submit" value="註冊">
</form>

<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $phone = $_POST['phone'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $name = $_POST['name'];

    $stmt = $conn->prepare("INSERT INTO customer (name, phone, password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $phone, $password);
    $stmt->execute();

    echo "註冊成功！";
}
?>
