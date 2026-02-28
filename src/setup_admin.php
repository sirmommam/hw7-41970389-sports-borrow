<?php
require_once 'config/db.php';

$username = 'admin';
$password = '123';
// เจนค่า Hash จาก PHP โดยตรง
$hashed_password = password_hash($password, PASSWORD_DEFAULT);

try {
    // ลบ User เก่า (ถ้ามี) แล้วเพิ่มใหม่
    $conn->prepare("DELETE FROM users WHERE username = :u")->execute([':u' => $username]);
    
    $sql = "INSERT INTO users (username, password, fullname, role) VALUES (:u, :p, 'Administrator', 'admin')";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':u' => $username, ':p' => $hashed_password]);

    echo "✅ อัปเดต User admin เรียบร้อย!<br>";
    echo "Username: admin<br>";
    echo "Password: 123<br>";
    echo "<a href='index.php'>กลับไปหน้า Login</a>";
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage();
}