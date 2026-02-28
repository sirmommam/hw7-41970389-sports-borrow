<?php
/**
 * Auth API สำหรับระบบยืม-คืนอุปกรณ์กีฬา
 * จัดการเรื่อง Login และ Session Control
 */
session_start();
require_once '../config/db.php'; // เรียกใช้ไฟล์เชื่อมต่อ DB ด้วย PDO 

// กำหนด Header ให้ส่งค่ากลับเป็น JSON เสมอ
header('Content-Type: application/json; charset=utf-8');

// ตรวจสอบว่าเป็น Request แบบ POST และมีการส่ง action=login มาหรือไม่
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'login') {
    
    // รับค่าและตัดช่องว่าง (Trim) เพื่อความสะอาดของข้อมูล 
    $username = isset($_POST['username']) ? trim($_POST['username']) : '';
    $password = isset($_POST['password']) ? trim($_POST['password']) : '';

    // 1. ตรวจสอบว่ากรอกข้อมูลครบหรือไม่
    if (empty($username) || empty($password)) {
        echo json_encode([
            'status' => 'error', 
            'message' => 'กรุณากรอกชื่อผู้ใช้งานและรหัสผ่านให้ครบถ้วน'
        ]);
        exit;
    }

    try {
        // 2. เตรียม Query ตรวจสอบ User (ใช้ Prepared Statement เพื่อความปลอดภัย) 
        $sql = "SELECT id, username, password, fullname, role FROM users WHERE username = :username LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':username', $username, PDO::PARAM_STR);
        $stmt->execute();
        
        $user = $stmt->fetch();

        if ($user) {
            // 3. ตรวจสอบรหัสผ่านที่รับมากับรหัสผ่านแบบ Hash ใน Database 
            if (password_verify($password, $user['password'])) {
                
                // เริ่มต้นสร้าง Session เมื่อ Login สำเร็จ 
                $_SESSION['user_id']   = $user['id'];
                $_SESSION['username']  = $user['username'];
                $_SESSION['fullname']  = $user['fullname'];
                $_SESSION['role']      = $user['role'];
                $_SESSION['is_logged_in'] = true;

                // ส่งค่ากลับไปที่หน้า index.php (AJAX)
                echo json_encode([
                    'status' => 'success', 
                    'message' => 'เข้าสู่ระบบสำเร็จ! ยินดีต้อนรับคุณ ' . $user['fullname']
                ]);
            } else {
                // กรณีรหัสผ่านไม่ตรง
                echo json_encode([
                    'status' => 'error', 
                    'message' => 'รหัสผ่านไม่ถูกต้อง กรุณาลองใหม่อีกครั้ง'
                ]);
            }
        } else {
            // กรณียังไม่มี Username นี้ในระบบ
            echo json_encode([
                'status' => 'error', 
                'message' => 'ไม่พบชื่อผู้ใช้งานนี้ในระบบ'
            ]);
        }

    } catch (PDOException $e) {
        // กรณีเกิด Error จาก Database
        echo json_encode([
            'status' => 'error', 
            'message' => 'เกิดข้อผิดพลาดในการเชื่อมต่อ: ' . $e->getMessage()
        ]);
    }
} else {
    // กรณีพยายามเข้าถึงไฟล์โดยตรงโดยไม่ผ่าน Form
    echo json_encode([
        'status' => 'error', 
        'message' => 'การเข้าถึงไม่ถูกต้อง (Invalid Request)'
    ]);
}