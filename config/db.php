<?php
// 🚨 โฮสต์ไม่ใช่ localhost แต่เป็นชื่อ Service ใน docker-compose.yml
$host = 'db'; 
$dbname = 'sports_borrow_db';
$username = 'dev_user';
$password = 'dev_password';

try {
    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    $conn = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    die("🚨 เชื่อมต่อฐานข้อมูลไม่สำเร็จ: " . $e->getMessage());
}
?>
