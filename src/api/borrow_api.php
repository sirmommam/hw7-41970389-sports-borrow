<?php
session_start();
require_once '../config/db.php';
header('Content-Type: application/json');

if (!isset($_SESSION['is_logged_in'])) {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized']);
    exit;
}

$action = $_POST['action'] ?? '';
$user_id = $_SESSION['user_id'];

if ($action === 'borrow') {
    $eq_id = (int)$_POST['eq_id'];

    try {
        $conn->beginTransaction();

        // 1. ตรวจสอบว่าของยังเหลือไหม
        $stmt = $conn->prepare("SELECT remain_qty, eq_name FROM equipments WHERE eq_id = :id FOR UPDATE");
        $stmt->execute([':id' => $eq_id]);
        $item = $stmt->fetch();

        if ($item && $item['remain_qty'] > 0) {
            // 2. ลดจำนวนคงเหลือ
            $update = $conn->prepare("UPDATE equipments SET remain_qty = remain_qty - 1 WHERE eq_id = :id");
            $update->execute([':id' => $eq_id]);

            // 3. บันทึกประวัติการยืม
            $insert = $conn->prepare("INSERT INTO borrows (eq_id, user_id, status) VALUES (:eq, :user, 'borrowed')");
            $insert->execute([':eq' => $eq_id, ':user' => $user_id]);

            $conn->commit();
            echo json_encode(['status' => 'success', 'message' => 'ยืม ' . $item['eq_name'] . ' สำเร็จ!']);
        } else {
            $conn->rollBack();
            echo json_encode(['status' => 'error', 'message' => 'ขออภัย อุปกรณ์นี้ถูกยืมไปหมดแล้ว']);
        }
    } catch (Exception $e) {
        $conn->rollBack();
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
}