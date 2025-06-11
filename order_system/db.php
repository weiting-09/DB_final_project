<?php
$host = 'localhost';
$db   = 'order_system';  // 你的資料庫名稱
$user = 'root';          // 使用者名稱
$pass = 'A123456789';    // 密碼
$charset = 'utf8';

$dsn = "mysql:host=$host;dbname=$db;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (\PDOException $e) {
    exit("資料庫連線失敗: " . $e->getMessage());
}
?>
