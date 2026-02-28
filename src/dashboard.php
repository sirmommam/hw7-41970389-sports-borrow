<?php
session_start();
/**
 * 🛡️ Security Check
 */
if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    header('Location: index.php');
    exit;
}
require_once 'config/db.php';
$fullname = $_SESSION['fullname'];
$role = $_SESSION['role'];

try {
    // 📊 1. ดึงสถิติจริงจากฐานข้อมูล
    // จำนวนรายการอุปกรณ์ทั้งหมด (นับจำนวนแถว)
    $stmt_total = $conn->query("SELECT COUNT(*) FROM equipments");
    $total_types = $stmt_total->fetchColumn() ?? 0;

    // จำนวนชิ้นอุปกรณ์ทั้งหมดในคลัง (SUM total_qty)
    $stmt_items = $conn->query("SELECT SUM(total_qty) FROM equipments");
    $total_items = $stmt_items->fetchColumn() ?? 0;

    // จำนวนที่ถูกยืมไป (Total - Remain)
    $stmt_borrowed = $conn->query("SELECT (SUM(total_qty) - SUM(remain_qty)) FROM equipments");
    $borrowed_count = $stmt_borrowed->fetchColumn() ?? 0;

    // จำนวนที่พร้อมใช้งาน
    $stmt_remain = $conn->query("SELECT SUM(remain_qty) FROM equipments");
    $remain_count = $stmt_remain->fetchColumn() ?? 0;

    // 🕒 2. ดึงรายการยืมล่าสุด 5 รายการ (JOIN กับตาราง users และ equipments)
    $sql_recent = "SELECT b.*, e.eq_name, u.fullname as borrower 
                   FROM borrows b
                   JOIN equipments e ON b.eq_id = e.eq_id
                   JOIN users u ON b.user_id = u.id
                   ORDER BY b.borrow_date DESC LIMIT 5";
    $recent_activities = $conn->query($sql_recent)->fetchAll();

} catch (PDOException $e) {
    $error_msg = $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>แดชบอร์ด | Sports Borrowing System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style> body { font-family: 'Prompt', sans-serif; } </style>
</head>
<body class="bg-slate-50 flex overflow-hidden">

    <?php include 'includes/sidebar.php'; ?>

    <div class="flex-1 flex flex-col min-w-0 h-screen">
        <header class="h-16 bg-white border-b border-slate-200 flex items-center justify-between px-8 shrink-0">
            <h2 class="text-slate-800 font-bold text-lg">สรุปภาพรวมระบบ</h2>
            <div class="flex items-center space-x-4">
                <span class="text-sm text-slate-500 italic"><?php echo date('d F 2026'); ?></span>
                <div class="w-8 h-8 rounded-full bg-blue-600 flex items-center justify-center text-white text-xs">
                    <?php echo mb_substr($fullname, 0, 1, 'UTF-8'); ?>
                </div>
            </div>
        </header>

        <main class="flex-1 overflow-y-auto p-8">
            <div class="bg-gradient-to-br from-blue-600 to-indigo-700 rounded-[2rem] p-8 text-white mb-8 shadow-xl shadow-blue-100 relative overflow-hidden">
                <div class="relative z-10">
                    <h1 class="text-3xl font-bold">สวัสดีครับคุณ <?php echo $fullname; ?> 👋</h1>
                    <p class="mt-2 text-blue-100 opacity-90">ยินดีต้อนรับสู่ระบบจัดการอุปกรณ์กีฬา ศรีปทุม ชลบุรี</p>
                    <div class="mt-6">
                        <a href="borrow.php" class="bg-white text-blue-600 px-6 py-2.5 rounded-xl font-bold text-sm hover:bg-blue-50 transition-all inline-flex items-center shadow-lg">
                            <i class="fas fa-plus-circle mr-2"></i> เริ่มทำรายการยืม
                        </a>
                    </div>
                </div>
                <i class="fas fa-medal absolute right-[-20px] bottom-[-20px] text-[15rem] opacity-10 rotate-12"></i>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm">
                    <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center mb-4">
                        <i class="fas fa-list-check text-xl"></i>
                    </div>
                    <p class="text-slate-400 text-xs font-bold uppercase tracking-wider">ประเภทอุปกรณ์</p>
                    <h3 class="text-2xl font-black text-slate-800 mt-1"><?php echo number_format($total_types); ?> <span class="text-xs font-normal">รายการ</span></h3>
                </div>

                <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm text-blue-600">
                    <div class="w-12 h-12 bg-blue-600 text-white rounded-2xl flex items-center justify-center mb-4 shadow-lg shadow-blue-100">
                        <i class="fas fa-box text-xl"></i>
                    </div>
                    <p class="text-slate-400 text-xs font-bold uppercase tracking-wider">จำนวนชิ้นทั้งหมด</p>
                    <h3 class="text-2xl font-black text-slate-800 mt-1"><?php echo number_format($total_items); ?> <span class="text-xs font-normal">ชิ้น</span></h3>
                </div>

                <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm text-amber-500">
                    <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center mb-4">
                        <i class="fas fa-hand-holding-heart text-xl"></i>
                    </div>
                    <p class="text-slate-400 text-xs font-bold uppercase tracking-wider">ถูกยืมออกไป</p>
                    <h3 class="text-2xl font-black text-slate-800 mt-1"><?php echo number_format($borrowed_count); ?> <span class="text-xs font-normal">ชิ้น</span></h3>
                </div>

                <div class="bg-white p-6 rounded-3xl border border-slate-100 shadow-sm text-emerald-500">
                    <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-2xl flex items-center justify-center mb-4">
                        <i class="fas fa-check-circle text-xl"></i>
                    </div>
                    <p class="text-slate-400 text-xs font-bold uppercase tracking-wider">คงเหลือพร้อมใช้</p>
                    <h3 class="text-2xl font-black text-slate-800 mt-1"><?php echo number_format($remain_count); ?> <span class="text-xs font-normal">ชิ้น</span></h3>
                </div>
            </div>

            <div class="bg-white rounded-[2rem] shadow-sm border border-slate-100 overflow-hidden">
                <div class="p-6 border-b border-slate-50 flex items-center justify-between">
                    <h3 class="font-bold text-slate-800">ประวัติการทำรายการล่าสุด</h3>
                    <span class="px-3 py-1 bg-slate-100 text-slate-500 text-[10px] font-bold rounded-full uppercase">Real-time Data</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-slate-50/50 text-slate-400 text-[10px] uppercase tracking-widest font-bold">
                            <tr>
                                <th class="px-8 py-4">ผู้ยืม</th>
                                <th class="px-8 py-4">อุปกรณ์</th>
                                <th class="px-8 py-4 text-center">วันที่ทำรายการ</th>
                                <th class="px-8 py-4 text-center">สถานะ</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50">
                            <?php if (count($recent_activities) > 0): ?>
                                <?php foreach ($recent_activities as $row): ?>
                                <tr class="hover:bg-slate-50/50 transition-colors">
                                    <td class="px-8 py-4">
                                        <div class="flex items-center">
                                            <div class="w-7 h-7 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center mr-3 text-[10px] font-bold">
                                                <?php echo mb_substr($row['borrower'], 0, 1, 'UTF-8'); ?>
                                            </div>
                                            <span class="text-sm font-medium text-slate-700"><?php echo $row['borrower']; ?></span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-4 text-sm font-bold text-slate-800"><?php echo $row['eq_name']; ?></td>
                                    <td class="px-8 py-4 text-center text-xs text-slate-400 font-mono"><?php echo date('d/m/Y H:i', strtotime($row['borrow_date'])); ?></td>
                                    <td class="px-8 py-4 text-center">
                                        <?php 
                                            $status_class = $row['status'] == 'borrowed' ? 'bg-amber-100 text-amber-600' : 'bg-emerald-100 text-emerald-600';
                                            $status_text = $row['status'] == 'borrowed' ? 'กำลังยืม' : 'คืนแล้ว';
                                        ?>
                                        <span class="px-3 py-1 <?php echo $status_class; ?> rounded-full text-[10px] font-black uppercase"><?php echo $status_text; ?></span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="4" class="px-8 py-10 text-center text-slate-400 italic">ยังไม่มีความเคลื่อนไหวในขณะนี้</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>
</body>
</html>