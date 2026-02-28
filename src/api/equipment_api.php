<?php
/**
 * Equipment API - จัดการข้อมูลอุปกรณ์กีฬา (CRUD) 
 * ปรับปรุงการจัดการพารามิเตอร์และการอัปโหลดไฟล์ 
 */
session_start();
require_once '../config/db.php'; 

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    echo json_encode(['status' => 'error', 'message' => 'กรุณาเข้าสู่ระบบก่อนใช้งาน']);
    exit;
}

$action = $_REQUEST['action'] ?? '';

try {
    // 🔍 1. ดึงข้อมูลทั้งหมด (Fetch) 
    if ($action === 'fetch') {
        $stmt = $conn->prepare("SELECT * FROM equipments ORDER BY eq_id DESC");
        $stmt->execute();
        echo json_encode($stmt->fetchAll());
        exit;
    }

    // 🔍 2. ดึงข้อมูลรายชิ้น (Get Single Item - สำหรับ Edit Modal) 
    if ($action === 'get_item' && isset($_GET['eq_id'])) {
        $stmt = $conn->prepare("SELECT * FROM equipments WHERE eq_id = :id");
        $stmt->execute([':id' => $_GET['eq_id']]);
        echo json_encode($stmt->fetch());
        exit;
    }

    // ➕ 3. เพิ่มข้อมูล (Add) 
    if ($action === 'add') {
        $name = trim($_POST['eq_name'] ?? '');
        $type = $_POST['eq_type'] ?? 'อื่นๆ';
        $qty  = (int)($_POST['total_qty'] ?? 0);
        $image_name = null;

        if (isset($_FILES['eq_image']) && $_FILES['eq_image']['error'] === 0) {
            $ext = pathinfo($_FILES['eq_image']['name'], PATHINFO_EXTENSION);
            $image_name = 'eq_' . time() . '_' . uniqid() . '.' . $ext;
            move_uploaded_file($_FILES['eq_image']['tmp_name'], "../uploads/" . $image_name);
        }

        $sql = "INSERT INTO equipments (eq_name, eq_type, total_qty, remain_qty, eq_image, status) 
                VALUES (:name, :type, :qty, :rem, :img, 'available')";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':name' => $name, 
            ':type' => $type, 
            ':qty'  => $qty, 
            ':rem'  => $qty,
            ':img'  => $image_name
        ]);

        echo json_encode(['status' => 'success', 'message' => 'เพิ่มอุปกรณ์เรียบร้อยแล้ว']);
        exit;
    }

    // 📝 4. แก้ไขข้อมูล (Update) 
    if ($action === 'update') {
        $id   = (int)$_POST['eq_id'];
        $name = trim($_POST['eq_name']);
        $type = $_POST['eq_type'];
        $qty  = (int)$_POST['total_qty'];

        $stmt_old = $conn->prepare("SELECT eq_image, total_qty, remain_qty FROM equipments WHERE eq_id = :id");
        $stmt_old->execute([':id' => $id]);
        $old_data = $stmt_old->fetch();

        $image_name = $old_data['eq_image'];

        if (isset($_FILES['eq_image']) && $_FILES['eq_image']['error'] === 0) {
            if ($image_name && file_exists("../uploads/" . $image_name)) {
                unlink("../uploads/" . $image_name);
            }
            $ext = pathinfo($_FILES['eq_image']['name'], PATHINFO_EXTENSION);
            $image_name = 'eq_' . time() . '_' . uniqid() . '.' . $ext;
            move_uploaded_file($_FILES['eq_image']['tmp_name'], "../uploads/" . $image_name);
        }

        $diff = $qty - $old_data['total_qty'];
        $new_remain = max(0, $old_data['remain_qty'] + $diff);

        $sql = "UPDATE equipments SET eq_name = :name, eq_type = :type, total_qty = :qty, remain_qty = :rem, eq_image = :img WHERE eq_id = :id";
        $stmt = $conn->prepare($sql);
        $stmt->execute([
            ':name' => $name,
            ':type' => $type,
            ':qty'  => $qty,
            ':rem'  => $new_remain,
            ':img'  => $image_name,
            ':id'   => $id
        ]);

        echo json_encode(['status' => 'success', 'message' => 'แก้ไขข้อมูลเรียบร้อยแล้ว']);
        exit;
    }

    // 🗑️ 5. ลบข้อมูล (Delete) 
    if ($action === 'delete') {
        $id = (int)$_POST['eq_id'];
        
        $stmt_img = $conn->prepare("SELECT eq_image FROM equipments WHERE eq_id = :id");
        $stmt_img->execute([':id' => $id]);
        $img = $stmt_img->fetchColumn();

        if ($img && file_exists("../uploads/" . $img)) {
            unlink("../uploads/" . $img);
        }

        $stmt = $conn->prepare("DELETE FROM equipments WHERE eq_id = :id");
        $stmt->execute([':id' => $id]);

        echo json_encode(['status' => 'success', 'message' => 'ลบข้อมูลสำเร็จ']);
        exit;
    }

} catch (PDOException $e) {
    echo json_encode(['status' => 'error', 'message' => 'Database Error: ' . $e->getMessage()]);
}